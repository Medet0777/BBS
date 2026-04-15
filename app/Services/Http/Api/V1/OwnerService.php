<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\OwnerRepositoryContract;
use App\Contracts\Services\Http\Api\V1\OwnerServiceContract;
use App\Enums\BookingStatus;
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

        // 0. Date ranges
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

        // 1. Today bookings count + delta vs yesterday
        $todayBookings     = $this->ownerRepository->countBookingsInRange($barbershop->id, $todayFrom, $todayTo, $excludeCancelled);
        $yesterdayBookings = $this->ownerRepository->countBookingsInRange($barbershop->id, $yesterdayFrom, $yesterdayTo, $excludeCancelled);
        $bookingsDelta     = $todayBookings - $yesterdayBookings;

        // 2. This week revenue + % change vs previous week
        $weekRevenue     = $this->ownerRepository->sumRevenueInRange($barbershop->id, $weekFrom, $weekTo, $excludeCancelled);
        $prevWeekRevenue = $this->ownerRepository->sumRevenueInRange($barbershop->id, $prevWeekFrom, $prevWeekTo, $excludeCancelled);
        $revenueDeltaPct = $prevWeekRevenue > 0
            ? round((($weekRevenue - $prevWeekRevenue) / $prevWeekRevenue) * 100, 1)
            : null;

        // 3. New clients this week
        $newClients = $this->ownerRepository->countNewClientsInRange($barbershop->id, $weekFrom, $weekTo, $excludeCancelled);

        // 4. Revenue per day (last 7 days, fill gaps with 0)
        $revenueRaw = $this->ownerRepository->revenuePerDayInRange($barbershop->id, $last7From, $last7To, $excludeCancelled);
        $revenue7d  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $revenue7d[] = [
                'date'    => $day,
                'revenue' => $revenueRaw[$day] ?? 0,
            ];
        }

        // 5. Nearest 3 bookings
        $nearest = $this->ownerRepository->getNearestBookings($barbershop->id, 3);

        return $this->success([
            'barbershop' => [
                'id'     => $barbershop->id,
                'name'   => $barbershop->name,
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
}
