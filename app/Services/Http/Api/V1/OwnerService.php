<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\OwnerRepositoryContract;
use App\Contracts\Services\Http\Api\V1\OwnerServiceContract;
use App\Enums\BookingStatus;
use App\Http\Requests\Api\V1\Owner\BookingListRequest;
use App\Http\Requests\Api\V1\Owner\CalendarRequest;
use App\Http\Requests\Api\V1\Owner\ServiceStoreRequest;
use App\Http\Requests\Api\V1\Owner\ServiceUpdateRequest;
use App\Http\Resources\Api\V1\Owner\BookingResource;
use App\Http\Resources\Api\V1\Owner\ServiceResource;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class OwnerService implements OwnerServiceContract
{

    use ApiResponse;

    /**
     * @param OwnerRepositoryContract $ownerRepository
     */
    public function __construct(
        private readonly OwnerRepositoryContract $ownerRepository,
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $excludeCancelled = [BookingStatus::Cancelled->value];

        $todayFrom     = Carbon::today()->toDateTimeString();
        $todayTo       = Carbon::today()->endOfDay()->toDateTimeString();
        $yesterdayFrom = Carbon::yesterday()->toDateTimeString();
        $yesterdayTo   = Carbon::yesterday()->endOfDay()->toDateTimeString();

        $weekFrom     = Carbon::now()->startOfWeek()->toDateTimeString();
        $weekTo       = Carbon::now()->endOfWeek()->toDateTimeString();
        $prevWeekFrom = Carbon::now()->subWeek()->startOfWeek()->toDateTimeString();
        $prevWeekTo   = Carbon::now()->subWeek()->endOfWeek()->toDateTimeString();

        $last7From = Carbon::now()->subDays(6)->startOfDay()->toDateTimeString();
        $last7To   = Carbon::now()->endOfDay()->toDateTimeString();

        $todayBookings     = $this->ownerRepository->countBookingsInRange($barbershop->id, $todayFrom, $todayTo, $excludeCancelled);
        $yesterdayBookings = $this->ownerRepository->countBookingsInRange($barbershop->id, $yesterdayFrom, $yesterdayTo, $excludeCancelled);
        $bookingsDelta     = $todayBookings - $yesterdayBookings;

        $weekRevenue     = $this->ownerRepository->sumRevenueInRange($barbershop->id, $weekFrom, $weekTo, $excludeCancelled);
        $prevWeekRevenue = $this->ownerRepository->sumRevenueInRange($barbershop->id, $prevWeekFrom, $prevWeekTo, $excludeCancelled);
        $revenueDeltaPct = $prevWeekRevenue > 0
            ? round((($weekRevenue - $prevWeekRevenue) / $prevWeekRevenue) * 100, 1)
            : null;

        $newClients = $this->ownerRepository->countNewClientsInRange($barbershop->id, $weekFrom, $weekTo, $excludeCancelled);

        $revenueRaw = $this->ownerRepository->revenuePerDayInRange($barbershop->id, $last7From, $last7To, $excludeCancelled);
        $revenue7d  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $revenue7d[] = [
                'date'    => $day,
                'revenue' => $revenueRaw[$day] ?? 0,
            ];
        }

        $nearest = $this->ownerRepository->getNearestBookings($barbershop->id, 3);

        return $this->success([
            'barbershop' => [
                'id'     => $barbershop->id,
                'name'   => $barbershop->name,
                'slug'   => $barbershop->slug,
                'rating' => $barbershop->avg_rating !== null ? round((float) $barbershop->avg_rating, 1) : 0,
            ],
            'today_bookings' => [
                'count'            => $todayBookings,
                'delta_vs_yesterday' => $bookingsDelta,
            ],
            'week_revenue' => [
                'amount'          => $weekRevenue,
                'delta_pct_vs_prev' => $revenueDeltaPct,
            ],
            'new_clients_this_week' => $newClients,
            'revenue_last_7_days'   => $revenue7d,
            'nearest_bookings'      => $nearest->map(fn ($booking) => [
                'id'             => $booking->id,
                'client_name'    => $booking->user?->name,
                'barber_name'    => $booking->barber?->name,
                'scheduled_at'   => $booking->scheduled_at,
                'services_count' => $booking->services->count(),
                'total_price'    => $booking->total_price,
                'status'         => $booking->status->value,
            ]),
        ]);
    }

    /**
     * @param CalendarRequest $request
     *
     * @return JsonResponse
     */
    public function calendar(CalendarRequest $request): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $from = Carbon::parse($request->input('from'))->startOfDay()->toDateTimeString();
        $to   = Carbon::parse($request->input('to'))->endOfDay()->toDateTimeString();

        $bookings = $this->ownerRepository->getBookingsInRange($barbershop->id, $from, $to);

        $grouped = $bookings
            ->groupBy(fn ($booking) => Carbon::parse($booking->scheduled_at)->toDateString())
            ->map(fn ($dayBookings, $date) => [
                'date'     => $date,
                'count'    => $dayBookings->count(),
                'bookings' => $dayBookings->map(fn ($booking) => [
                    'id'           => $booking->id,
                    'client_name'  => $booking->user?->name,
                    'barber_name'  => $booking->barber?->name,
                    'scheduled_at' => $booking->scheduled_at,
                    'total_price'  => $booking->total_price,
                    'status'       => $booking->status->value,
                ])->values(),
            ])
            ->values();

        return $this->success([
            'from'  => $request->input('from'),
            'to'    => $request->input('to'),
            'total' => $bookings->count(),
            'days'  => $grouped,
        ]);
    }

    /**
     * @param BookingListRequest $request
     *
     * @return JsonResponse
     */
    public function bookings(BookingListRequest $request): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $filter  = $request->input('filter', 'all');
        $perPage = $request->integer('per_page', 15);

        $bookings = $this->ownerRepository->getBookingsPaginated($barbershop->id, $filter, $perPage);

        return $this->success(BookingResource::collection($bookings)->response()->getData(true));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function cancelBooking(int $id): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $booking = $this->ownerRepository->findBookingByBarbershop($id, $barbershop->id);

        if (!$booking) {
            return $this->error('Booking not found', 'not_found', 404);
        }

        if (!in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)) {
            return $this->error('Only pending or confirmed bookings can be cancelled', 'invalid_status', 422);
        }

        $booking = $this->ownerRepository->updateBooking($booking, ['status' => BookingStatus::Cancelled->value]);

        return $this->success([
            'id'     => $booking->id,
            'status' => $booking->status->value,
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function completeBooking(int $id): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $booking = $this->ownerRepository->findBookingByBarbershop($id, $barbershop->id);

        if (!$booking) {
            return $this->error('Booking not found', 'not_found', 404);
        }

        if ($booking->status !== BookingStatus::Confirmed) {
            return $this->error('Only confirmed bookings can be marked as completed', 'invalid_status', 422);
        }

        $booking = $this->ownerRepository->updateBooking($booking, ['status' => BookingStatus::Completed->value]);

        return $this->success([
            'id'     => $booking->id,
            'status' => $booking->status->value,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function listServices(): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $services = $this->ownerRepository->getServices($barbershop->id);

        return $this->success(ServiceResource::collection($services));
    }

    /**
     * @param ServiceStoreRequest $request
     *
     * @return JsonResponse
     */
    public function createService(ServiceStoreRequest $request): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $category = $this->ownerRepository->firstOrCreateCategory($request->input('category_name'));

        $service = $this->ownerRepository->createService([
            'barbershop_id'       => $barbershop->id,
            'service_category_id' => $category->id,
            'name'                => $request->input('name'),
            'price'               => $request->integer('price'),
            'duration_minutes'    => $request->integer('duration_minutes'),
        ]);

        return $this->success(new ServiceResource($service), 'Service created', 201);
    }

    /**
     * @param int                  $id
     * @param ServiceUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function updateService(int $id, ServiceUpdateRequest $request): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $service = $this->ownerRepository->findServiceByBarbershop($id, $barbershop->id);

        if (!$service) {
            return $this->error('Service not found', 'not_found', 404);
        }

        $data = array_filter([
            'name'             => $request->input('name'),
            'price'            => $request->filled('price') ? $request->integer('price') : null,
            'duration_minutes' => $request->filled('duration_minutes') ? $request->integer('duration_minutes') : null,
        ], fn ($value) => $value !== null);

        if ($request->filled('category_name')) {
            $category = $this->ownerRepository->firstOrCreateCategory($request->input('category_name'));
            $data['service_category_id'] = $category->id;
        }

        $service = $this->ownerRepository->updateService($service, $data);

        return $this->success(new ServiceResource($service), 'Service updated');
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteService(int $id): JsonResponse
    {
        $barbershop = $this->ownerRepository->findBarbershopByOwner(auth()->id());

        if (!$barbershop) {
            return $this->error('You do not own any barbershop', 'not_an_owner', 403);
        }

        $service = $this->ownerRepository->findServiceByBarbershop($id, $barbershop->id);

        if (!$service) {
            return $this->error('Service not found', 'not_found', 404);
        }

        if ($this->ownerRepository->serviceHasActiveBookings($id)) {
            return $this->error('Cannot delete service with active bookings', 'has_active_bookings', 422);
        }

        $this->ownerRepository->deleteService($service);

        return $this->success(null, 'Service deleted');
    }
}
