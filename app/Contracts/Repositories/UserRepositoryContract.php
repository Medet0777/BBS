<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryContract
{

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User;

    /**
     * @param string  $email
     * @param string  $name
     *
     * @return User
     */
    public function findOrCreateByGoogle(string $email, string $name): User;

    /**
     * @param User $user
     *
     * @return void
     */
    public function deleteCurrentToken(User $user): void;

    /**
     * @param User  $user
     * @param array $data
     *
     * @return User
     */
    public function update(User $user, array $data): User;
}
