<?php

namespace App\Contracts\Repositories;

use App\Models\PendingRegistration;

interface PendingRegistrationRepositoryContract
{

    /**
     * @param string $email
     * @param string $code
     *
     * @return PendingRegistration|null
     */
    public function findValidByEmailAndCode(string $email, string $code): ?PendingRegistration;

    /**
     * @param string $email
     *
     * @return PendingRegistration|null
     */
    public function findByEmail(string $email): ?PendingRegistration;

    /**
     * @param array $data
     *
     * @return PendingRegistration
     */
    public function create(array $data): PendingRegistration;

    /**
     * @param PendingRegistration $pending
     * @param array               $data
     *
     * @return void
     */
    public function update(PendingRegistration $pending, array $data): void;

    /**
     * @param PendingRegistration $pending
     *
     * @return void
     */
    public function delete(PendingRegistration $pending): void;

    /**
     * @param string $email
     *
     * @return void
     */
    public function deleteByEmail(string $email): void;
}
