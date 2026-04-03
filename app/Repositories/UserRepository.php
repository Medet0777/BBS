<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Models\User;

class UserRepository implements UserRepositoryContract
{

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
