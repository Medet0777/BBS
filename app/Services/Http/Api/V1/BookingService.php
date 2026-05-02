<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BookingRepositoryContract;
use App\Contracts\Services\Http\Api\V1\BookingServiceContract;
use App\Enums\BookingStatus;
use App\Http\Requests\Api\V1\Booking\CreateRequest;
use App\Http\Requests\Api\V1\Booking\ListRequest;
use App\Http\Requests\Api\V1\Booking\RescheduleRequest;
use App\Http\Resources\Api\V1\Booking\CreateResource;
use App\Http\Resources\Api\V1\Booking\ListResource;
use App\Jobs\SendBookingReminderJob;
use App\Models\Barber;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class BookingService implements BookingServiceContract
{

    use ApiResponse;

    /**
     * @param BookingRepositoryContract $bookingRepository
     */
    public function __construct(
        private readonly BookingRepositoryContract $bookingRepository,
    ) {
    }

    /**
     * @param CreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $barbershopId = $request->integer('barbershop_id');
        $barberId     = $request->filled('barber_id') ? $request->integer('barber_id') : null;
        $serviceIds   = $request->input('service_ids');
        $scheduledAt  = $request->input('scheduled_at');

        $services = $this->bookingRepository->getServicesByIds($serviceIds);

        if ($services->count() !== count($serviceIds)) {
            return $this->error('Some services were not found', 'invalid_services', 422);
        }

        if ($services->contains(fn ($s) => $s->barbershop_id !== $barbershopId)) {
            return $this->error('Services do not belong to this barbershop', 'invalid_services', 422);
        }

        $totalPrice    = 0;
        $totalDuration = 0;
        $pivot         = [];

        foreach ($services as $service) {
            $totalPrice    += (float) $service->price;
            $totalDuration += (int) $service->duration_minutes;
            $pivot[$service->id] = [
                'price_snapshot'    => $service->price,
                'duration_snapshot' => $service->duration_minutes,
            ];
        }

        if ($barberId === null) {
            $barber = $this->findAvailableBarber($barbershopId, $scheduledAt, $totalDuration);

            if (!$barber) {
                return $this->error('No available barber at this time', 'no_available_barber', 422);
            }

            $barberId = $barber->id;
        } else {
            $barber = Barber::where('id', $barberId)
                ->where('barbershop_id', $barbershopId)
                ->where('is_active', true)
                ->first();

            if (!$barber) {
                return $this->error('Barber does not belong to this barbershop', 'invalid_barber', 422);
            }
        }

        $booking = $this->bookingRepository->create([
            'user_id'                => auth()->id(),
            'barbershop_id'          => $barbershopId,
            'barber_id'              => $barberId,
            'scheduled_at'           => $scheduledAt,
            'status'                 => BookingStatus::Pending->value,
            'comment'                => $request->input('comment'),
            'reminder_enabled'       => $request->boolean('reminder_enabled'),
            'total_price'            => $totalPrice,
            'total_duration_minutes' => $totalDuration,
        ], $pivot);

        if ($booking->reminder_enabled) {
            $reminderAt = Carbon::parse($booking->scheduled_at)->subHours(2);

            if ($reminderAt->isFuture()) {
                SendBookingReminderJob::dispatch($booking->id)->delay($reminderAt);
            }
        }

        return $this->success(new CreateResource($booking), 'Booking created', 201);
    }

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        $filter  = $request->input('filter');
        $perPage = $request->integer('per_page', 10);

        $bookings = $this->bookingRepository->getUserBookings(auth()->id(), $filter, $perPage);

        return $this->success(ListResource::collection($bookings)->response()->getData(true));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->findForUser($id, auth()->id());

        if (!$booking) {
            return $this->error('Booking not found', 'not_found', 404);
        }

        return $this->success(new CreateResource($booking));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->findForUser($id, auth()->id());

        if (!$booking) {
            return $this->error('Booking not found', 'not_found', 404);
        }

        if (in_array($booking->status, [BookingStatus::Cancelled, BookingStatus::Completed], true)) {
            return $this->error('Booking can no longer be cancelled', 'invalid_status', 422);
        }

        $this->bookingRepository->update($booking, ['status' => BookingStatus::Cancelled->value]);

        return $this->success(null, 'Booking cancelled');
    }

    /**
     * @param int               $id
     * @param RescheduleRequest $request
     *
     * @return JsonResponse
     */
    public function reschedule(int $id, RescheduleRequest $request): JsonResponse
    {
        $booking = $this->bookingRepository->findForUser($id, auth()->id());

        if (!$booking) {
            return $this->error('Booking not found', 'not_found', 404);
        }

        if (in_array($booking->status, [BookingStatus::Cancelled, BookingStatus::Completed], true)) {
            return $this->error('Booking can no longer be rescheduled', 'invalid_status', 422);
        }

        $booking = $this->bookingRepository->update($booking, [
            'scheduled_at' => $request->input('scheduled_at'),
        ]);

        return $this->success(new CreateResource($booking), 'Booking rescheduled');
    }

    /**
     * @param int    $barbershopId
     * @param string $scheduledAt
     * @param int    $duration
     *
     * @return Barber|null
     */
    private function findAvailableBarber(int $barbershopId, string $scheduledAt, int $duration): ?Barber
    {
        $start = Carbon::parse($scheduledAt);
        $end   = $start->copy()->addMinutes($duration);

        $barbers = Barber::where('barbershop_id', $barbershopId)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        foreach ($barbers as $barber) {
            $hasOverlap = \App\Models\Booking::where('barber_id', $barber->id)
                ->whereNotIn('status', [BookingStatus::Cancelled->value])
                ->where('scheduled_at', '<', $end)
                ->whereRaw('DATE_ADD(scheduled_at, INTERVAL total_duration_minutes MINUTE) > ?', [$start])
                ->exists();

            if (!$hasOverlap) {
                return $barber;
            }
        }

        return null;
    }
}
