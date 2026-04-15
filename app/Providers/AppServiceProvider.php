<?php

namespace App\Providers;

use App\Contracts\Repositories\BarbershopRepositoryContract;
use App\Contracts\Repositories\BookingRepositoryContract;
use App\Contracts\Repositories\PendingRegistrationRepositoryContract;
use App\Contracts\Repositories\ReviewRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\Http\Api\V1\AuthServiceContract;
use App\Contracts\Services\Http\Api\V1\BarbershopServiceContract;
use App\Contracts\Services\Http\Api\V1\BookingServiceContract;
use App\Contracts\Services\Http\Api\V1\ReviewServiceContract;
use App\Repositories\BarbershopRepository;
use App\Repositories\BookingRepository;
use App\Repositories\PendingRegistrationRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\UserRepository;
use App\Services\Http\Api\V1\AuthService;
use App\Services\Http\Api\V1\BarbershopService;
use App\Services\Http\Api\V1\BookingService;
use App\Services\Http\Api\V1\ReviewService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(PendingRegistrationRepositoryContract::class, PendingRegistrationRepository::class);
        $this->app->bind(BarbershopRepositoryContract::class, BarbershopRepository::class);
        $this->app->bind(ReviewRepositoryContract::class, ReviewRepository::class);
        $this->app->bind(BookingRepositoryContract::class, BookingRepository::class);

        // Services
        $this->app->bind(AuthServiceContract::class, AuthService::class);
        $this->app->bind(BarbershopServiceContract::class, BarbershopService::class);
        $this->app->bind(ReviewServiceContract::class, ReviewService::class);
        $this->app->bind(BookingServiceContract::class, BookingService::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
