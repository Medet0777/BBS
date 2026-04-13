<?php

namespace App\Repositories;

use App\Contracts\Repositories\PendingRegistrationRepositoryContract;
use App\Models\PendingRegistration;
use Illuminate\Support\Carbon;

class PendingRegistrationRepository implements PendingRegistrationRepositoryContract
{

    /**
     * @param string $email
     * @param string $code
     *
     * @return PendingRegistration|null
     */
    public function findValidByEmailAndCode(string $email, string $code): ?PendingRegistration
    {
        return PendingRegistration::where('email', $email)
            ->where('otp_code', $code)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * @param string $email
     *
     * @return PendingRegistration|null
     */
    public function findByEmail(string $email): ?PendingRegistration
    {
        return PendingRegistration::where('email', $email)->first();
    }

    /**
     * @param array $data
     *
     * @return PendingRegistration
     */
    public function create(array $data): PendingRegistration
    {
        return PendingRegistration::create($data);
    }

    /**
     * @param PendingRegistration $pending
     * @param array               $data
     *
     * @return void
     */
    public function update(PendingRegistration $pending, array $data): void
    {
        $pending->update($data);
    }

    /**
     * @param PendingRegistration $pending
     *
     * @return void
     */
    public function delete(PendingRegistration $pending): void
    {
        $pending->delete();
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function deleteByEmail(string $email): void
    {
        PendingRegistration::where('email', $email)->delete();
    }
}
