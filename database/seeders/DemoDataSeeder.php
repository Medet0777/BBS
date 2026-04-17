<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Barbershop;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{

    /**
     * @return void
     */
    public function run(): void
    {
        $this->createUsers();
        $categories = $this->createServiceCategories();
        $this->createBarbershops($categories);
        $this->createBookings();
    }

    /**
     * @return void
     */
    private function createUsers(): void
    {
        $users = [
            ['name' => 'Admin',   'email' => 'admin@bbs.kz',   'is_owner' => false],
            ['name' => 'Owner 1', 'email' => 'owner1@bbs.kz',  'is_owner' => true],
            ['name' => 'Owner 2', 'email' => 'owner2@bbs.kz',  'is_owner' => true],
            ['name' => 'Aslan',   'email' => 'aslan@bbs.kz',   'is_owner' => false],
            ['name' => 'Aidar',   'email' => 'aidar@bbs.kz',   'is_owner' => false],
            ['name' => 'Azamat',  'email' => 'azamat@bbs.kz',  'is_owner' => false],
            ['name' => 'Damir',   'email' => 'damir@bbs.kz',   'is_owner' => false],
            ['name' => 'Medet',   'email' => 'medet@bbs.kz',   'is_owner' => false],
            ['name' => 'Rustem',  'email' => 'rustem@bbs.kz',  'is_owner' => false],
            ['name' => 'Yernar',  'email' => 'yernar@bbs.kz',  'is_owner' => false],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }

    /**
     * @return array<string, ServiceCategory>
     */
    private function createServiceCategories(): array
    {
        $map = [];
        $data = [
            ['name' => 'Стрижки', 'icon' => '✂️',  'sort_order' => 1],
            ['name' => 'Борода',  'icon' => '🧔',  'sort_order' => 2],
            ['name' => 'Бритьё',  'icon' => '🪒',  'sort_order' => 3],
            ['name' => 'Уход',    'icon' => '💆',  'sort_order' => 4],
            ['name' => 'Комплекс','icon' => '💎',  'sort_order' => 5],
        ];

        foreach ($data as $row) {
            $category = ServiceCategory::firstOrCreate(['name' => $row['name']], $row);
            $map[$row['name']] = $category;
        }

        return $map;
    }

    /**
     * @param array $categories
     *
     * @return void
     */
    private function createBarbershops(array $categories): void
    {
        $owner1 = User::where('email', 'owner1@bbs.kz')->first();
        $owner2 = User::where('email', 'owner2@bbs.kz')->first();

        $data = $this->barbershopData();

        foreach ($data as $index => $shopData) {
            $ownerId = null;
            if ($index === 0) $ownerId = $owner1?->id;
            if ($index === 1) $ownerId = $owner2?->id;

            $barbershop = Barbershop::firstOrCreate(
                ['slug' => Str::slug($shopData['name']) . '-' . ($index + 1)],
                [
                    'owner_id'    => $ownerId,
                    'name'        => $shopData['name'],
                    'description' => 'Лучший барбершоп в городе с опытными мастерами',
                    'logo'        => null,
                    'phone'       => '+7 (727) 3' . str_pad((string) (100000 + $index), 6, '0', STR_PAD_LEFT),
                    'address'     => $shopData['address'],
                    'latitude'    => $shopData['latitude'],
                    'longitude'   => $shopData['longitude'],
                    'rating'      => 0,
                    'opens_at'    => '09:00',
                    'closes_at'   => '21:00',
                    'is_active'   => true,
                ],
            );

            $this->createServicesForBarbershop($barbershop, $shopData, $categories);
            $this->createBarbersForBarbershop($barbershop);
            $this->createReviewsForBarbershop($barbershop);
        }
    }

    /**
     * @param Barbershop $barbershop
     * @param array      $shopData
     * @param array      $categories
     *
     * @return void
     */
    private function createServicesForBarbershop(Barbershop $barbershop, array $shopData, array $categories): void
    {
        $priceMap = [];
        foreach ($shopData['prices'] as $priceRow) {
            $priceMap[mb_strtolower($priceRow['service_name'])] = $this->parsePrice($priceRow['price']);
        }

        foreach ($shopData['services'] as $serviceRow) {
            $category = $this->detectCategory($serviceRow['name'], $categories);
            $price    = $this->matchPrice($serviceRow['name'], $priceMap) ?? $this->defaultPrice($category->name);
            $duration = $this->defaultDuration($category->name);

            Service::firstOrCreate(
                [
                    'barbershop_id'       => $barbershop->id,
                    'service_category_id' => $category->id,
                    'name'                => $serviceRow['name'],
                ],
                [
                    'price'            => $price,
                    'duration_minutes' => $duration,
                ],
            );
        }
    }

    /**
     * @param Barbershop $barbershop
     *
     * @return void
     */
    private function createBarbersForBarbershop(Barbershop $barbershop): void
    {
        $firstNames = ['Alikhan', 'Arman', 'Bekzat', 'Daulet', 'Erlan', 'Nurlan', 'Timur', 'Ayan', 'Bakhyt', 'Kanat'];
        $lastNames  = ['Satybaldy', 'Nurmagambet', 'Amankulov', 'Abdrakhmanov', 'Zhumabek', 'Sadykov', 'Alpysbayev'];
        $specs      = ['Классические стрижки', 'Бороды', 'Fade и Undercut', 'Детские стрижки', 'Бритьё опасной бритвой'];

        $count = rand(2, 4);
        for ($i = 0; $i < $count; $i++) {
            Barber::create([
                'barbershop_id'    => $barbershop->id,
                'name'             => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                'avatar'           => null,
                'specialization'   => $specs[array_rand($specs)],
                'rating'           => round(rand(40, 50) / 10, 1),
                'experience_years' => rand(1, 12),
                'is_active'        => true,
            ]);
        }
    }

    /**
     * @param Barbershop $barbershop
     *
     * @return void
     */
    private function createReviewsForBarbershop(Barbershop $barbershop): void
    {
        $userIds = User::where('email', 'like', '%@bbs.kz')
            ->whereNotIn('email', ['owner1@bbs.kz', 'owner2@bbs.kz'])
            ->pluck('id')
            ->toArray();

        $comments = [
            'Отличный мастер, всё понравилось!',
            'Очень качественно и быстро',
            'Рекомендую всем, сервис на высоте',
            'Спасибо за работу, буду ходить теперь только сюда',
            'Хорошая атмосфера, приятные мастера',
            'Стрижка получилась именно такая как я хотел',
            'Классный барбершоп, приятно удивлён',
            null,
            'Приду ещё точно',
            'Отличная работа, спасибо!',
        ];

        $reviewsCount = rand(3, 8);
        $sum = 0;

        for ($i = 0; $i < $reviewsCount; $i++) {
            $rating = rand(3, 5);
            $sum   += $rating;

            Review::create([
                'user_id'       => $userIds[array_rand($userIds)],
                'barbershop_id' => $barbershop->id,
                'rating'        => $rating,
                'comment'       => $comments[array_rand($comments)],
                'created_at'    => Carbon::now()->subDays(rand(1, 60)),
                'updated_at'    => Carbon::now(),
            ]);
        }

        $barbershop->update(['rating' => round($sum / $reviewsCount, 1)]);
    }

    /**
     * @return void
     */
    private function createBookings(): void
    {
        $users       = User::where('email', 'like', '%@bbs.kz')
            ->whereNotIn('email', ['owner1@bbs.kz', 'owner2@bbs.kz'])
            ->get();
        $barbershops = Barbershop::with(['services', 'barbers'])->limit(10)->get();

        foreach ($barbershops as $barbershop) {
            if ($barbershop->barbers->isEmpty() || $barbershop->services->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < rand(2, 5); $i++) {
                $user      = $users->random();
                $barber    = $barbershop->barbers->random();
                $services  = $barbershop->services->random(rand(1, 2));
                $totalPrice    = $services->sum('price');
                $totalDuration = $services->sum('duration_minutes');

                $isPast = (bool) rand(0, 1);
                $scheduledAt = $isPast
                    ? Carbon::now()->subDays(rand(1, 20))
                    : Carbon::now()->addDays(rand(1, 14))->setTime(rand(10, 19), [0, 30][rand(0, 1)]);

                $statuses = $isPast
                    ? [BookingStatus::Completed->value, BookingStatus::Cancelled->value]
                    : [BookingStatus::Pending->value, BookingStatus::Confirmed->value];

                $booking = Booking::create([
                    'user_id'                => $user->id,
                    'barbershop_id'          => $barbershop->id,
                    'barber_id'              => $barber->id,
                    'scheduled_at'           => $scheduledAt,
                    'status'                 => $statuses[array_rand($statuses)],
                    'comment'                => rand(0, 1) ? 'Подкоротить по бокам' : null,
                    'reminder_enabled'       => (bool) rand(0, 1),
                    'total_price'            => $totalPrice,
                    'total_duration_minutes' => $totalDuration,
                ]);

                $pivot = [];
                foreach ($services as $service) {
                    $pivot[$service->id] = [
                        'price_snapshot'    => $service->price,
                        'duration_snapshot' => $service->duration_minutes,
                    ];
                }
                $booking->services()->attach($pivot);
            }
        }
    }

    /**
     * @param string $serviceName
     * @param array  $categories
     *
     * @return ServiceCategory
     */
    private function detectCategory(string $serviceName, array $categories): ServiceCategory
    {
        $lower = mb_strtolower($serviceName);

        if (str_contains($lower, 'комплекс') || str_contains($lower, 'комбо') || str_contains($lower, 'фарш')
            || str_contains($lower, 'vip') || str_contains($lower, 'отец') || str_contains($lower, 'сын')) {
            return $categories['Комплекс'];
        }

        if (str_contains($lower, 'борода') || str_contains($lower, 'бород')) {
            return $categories['Борода'];
        }

        if (str_contains($lower, 'бритье') || str_contains($lower, 'бритьё') || str_contains($lower, 'брить')) {
            return $categories['Бритьё'];
        }

        if (str_contains($lower, 'маска') || str_contains($lower, 'скраб') || str_contains($lower, 'массаж')
            || str_contains($lower, 'спа') || str_contains($lower, 'spa') || str_contains($lower, 'чистка')
            || str_contains($lower, 'уход') || str_contains($lower, 'укладка') || str_contains($lower, 'тонир')
            || str_contains($lower, 'камуфляж') || str_contains($lower, 'депиляция') || str_contains($lower, 'маникюр')
            || str_contains($lower, 'груминг') || str_contains($lower, 'завив')) {
            return $categories['Уход'];
        }

        return $categories['Стрижки'];
    }

    /**
     * @param string     $name
     * @param array      $priceMap
     *
     * @return float|null
     */
    private function matchPrice(string $name, array $priceMap): ?float
    {
        $lower = mb_strtolower($name);

        foreach ($priceMap as $key => $price) {
            if ($key === $lower || str_contains($lower, $key) || str_contains($key, $lower)) {
                return $price;
            }
        }

        return null;
    }

    /**
     * @param string|int $price
     *
     * @return float
     */
    private function parsePrice(string|int $price): float
    {
        if (is_int($price)) {
            return (float) $price;
        }

        preg_match('/(\d+)/u', $price, $matches);

        return (float) ($matches[1] ?? 5000);
    }

    /**
     * @param string $category
     *
     * @return float
     */
    private function defaultPrice(string $category): float
    {
        return match ($category) {
            'Стрижки'  => 5000,
            'Борода'   => 3500,
            'Бритьё'   => 3000,
            'Уход'     => 2500,
            'Комплекс' => 9000,
            default    => 5000,
        };
    }

    /**
     * @param string $category
     *
     * @return int
     */
    private function defaultDuration(string $category): int
    {
        return match ($category) {
            'Стрижки'  => 45,
            'Борода'   => 30,
            'Бритьё'   => 30,
            'Уход'     => 20,
            'Комплекс' => 75,
            default    => 30,
        };
    }

    /**
     * @return array
     */
    private function barbershopData(): array
    {
        return json_decode(file_get_contents(database_path('seeders/data/barbershops.json')), true);
    }
}
