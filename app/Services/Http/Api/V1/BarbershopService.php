<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
use App\Http\Requests\Api\V1\Barbershop\ListRequest;
use App\Http\Requests\Api\V1\Barbershop\SlotsRequest;
use App\Http\Resources\Api\V1\Barbershop\ListResource;
use App\Http\Resources\Api\V1\Barbershop\ShowResource;
use App\Models\Service;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class BarbershopService implements BarbershopServiceContract
{

    use ApiResponse;

    /**
     * @param BarbershopRepositoryContract $barbershopRepository
     */
    public function __construct(
        private readonly BarbershopRepositoryContract $barbershopRepository,
    ) {
    }

    /**
     * @param ListRequest $request
     *
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        $perPage  = $request->integer('per_page', 10);
        $orderBy  = $request->input('order_by');
        $userLat  = $request->filled('user_lat') ? (float) $request->input('user_lat') : null;
        $userLng  = $request->filled('user_lng') ? (float) $request->input('user_lng') : null;
        $onlyOpen = $request->boolean('is_open');
        $search   = $request->input('search');

        $barbershops = $this->barbershopRepository->getAll($perPage, $orderBy, $userLat, $userLng, $onlyOpen, $search);

        return $this->success(ListResource::collection($barbershops)->response()->getData(true));
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $barbershop = $this->barbershopRepository->findBySlug($slug);

        if (!$barbershop) {
            return $this->error('Barbershop not found', 'not_found', 404);
        }

        return $this->success(new ShowResource($barbershop));
    }

    /**
     * @param string       $slug
     * @param SlotsRequest $request
     *
     * @return JsonResponse
     */
    public function availableSlots(string $slug, SlotsRequest $request): JsonResponse
    {
        $barbershop = $this->barbershopRepository->findBySlug($slug);

        if (!$barbershop) {
            return $this->error('Barbershop not found', 'not_found', 404);
        }

        $date      = $request->input('date');
        $barberId  = $request->filled('barber_id') ? $request->integer('barber_id') : null;
        $serviceId = $request->filled('service_id') ? $request->integer('service_id') : null;

        // 0. Determine requested service duration (default 30 min slot)
        $slotDuration = 30;
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service && $service->duration_minutes > 0) {
                $slotDuration = (int) $service->duration_minutes;
            }
        }

        // 1. Build all slots from opens_at to closes_at in 30-min steps
        $slots   = [];
        $cursor  = Carbon::parse($date . ' ' . $barbershop->opens_at);
        $closeAt = Carbon::parse($date . ' ' . $barbershop->closes_at);
        $now     = Carbon::now();

        while ($cursor->copy()->addMinutes($slotDuration) <= $closeAt) {
            $slots[] = $cursor->copy();
            $cursor->addMinutes(30);
        }

        // 2. Load existing bookings for the date/barber
        $bookings = $this->barbershopRepository->getBookingsForDate($barbershop->id, $date, $barberId);

        // 3. Mark slot availability: not in past AND no booking overlap
        $result = [];
        foreach ($slots as $slotStart) {
            $slotEnd   = $slotStart->copy()->addMinutes($slotDuration);
            $available = $slotStart->isFuture() || $slotStart->equalTo($now);

            if ($available) {
                foreach ($bookings as $booking) {
                    $bookingStart = Carbon::parse($booking->scheduled_at);
                    $bookingEnd   = $bookingStart->copy()->addMinutes((int) $booking->total_duration_minutes);

                    if ($slotStart < $bookingEnd && $slotEnd > $bookingStart) {
                        $available = false;
                        break;
                    }
                }
            }

            $result[] = [
                'time'      => $slotStart->format('H:i'),
                'available' => $available,
            ];
        }

        return $this->success($result);
    }
}
