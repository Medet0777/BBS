<?php

namespace App\Providers;

use App\Contracts\Repositories\PendingRegistrationRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\Http\Api\V1\AuthServiceContract;
use App\Repositories\PendingRegistrationRepository;
use App\Repositories\UserRepository;
use App\Services\Http\Api\V1\AuthService;
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

        // Services
        $this->app->bind(AuthServiceContract::class, AuthService::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
