<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\PendingRegistrationRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\Http\Api\V1\AuthServiceContract;
use App\Dto\Services\Http\V1\Auth\GoogleLoginDto;
use App\Dto\Services\Http\V1\Auth\LoginDto;
use App\Dto\Services\Http\V1\Auth\RegisterDto;
use App\Dto\Services\Http\V1\Auth\ResendCodeDto;
use App\Dto\Services\Http\V1\Auth\VerifyEmailDto;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResendCodeRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Http\Resources\Api\V1\Auth\TokenResource;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Mail\OtpCodeMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Google_Client;

class AuthService implements AuthServiceContract
{

    use ApiResponse;

    /**
     * @param UserRepositoryContract                $userRepository
     * @param PendingRegistrationRepositoryContract $pendingRepository
     */
    public function __construct(
        private readonly UserRepositoryContract                $userRepository,
        private readonly PendingRegistrationRepositoryContract $pendingRepository,
    ) {
    }

    /**
     * @param \App\Http\Requests\Api\V1\Auth\RegisterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Random\RandomException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDto::from($request->validated());

        $this->pendingRepository->deleteByEmail($dto->email);

        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->pendingRepository->create([
            'name'           => $dto->name,
            'email'          => $dto->email,
            'password'       => Hash::make($dto->password),
            'otp_code'       => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($dto->email)->queue(new OtpCodeMail($otpCode));

        return $this->success(null, 'Verification code sent to email');
    }

    /**
     * @param VerifyEmailRequest $request
     *
     * @return JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $dto = VerifyEmailDto::from($request->validated());

        $pending = $this->pendingRepository->findValidByEmailAndCode($dto->email, $dto->code);

        if (!$pending) {
            return $this->error('Invalid or expired code', 'invalid_otp', 422);
        }

        $user = $this->userRepository->create([
            'name'              => $pending->name,
            'email'             => $pending->email,
            'password'          => $pending->password,
            'email_verified_at' => Carbon::now(),
        ]);

        $this->pendingRepository->delete($pending);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new TokenResource($user, $token), 'Registration successful', 201);
    }

    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDto::from($request->validated());

        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            return $this->error('Invalid email or password', 'invalid_credentials', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new TokenResource($user, $token), 'Login successful');
    }

    /**
     * @param GoogleLoginRequest $request
     *
     * @return JsonResponse
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $dto = GoogleLoginDto::from($request->validated());

        $googleClient = new Google_Client(['client_id' => config('services.google.client_id')]);
        $payload      = $googleClient->verifyIdToken($dto->id_token);

        if (!$payload) {
            return $this->error('Invalid Google token', 'invalid_google_token', 401);
        }

        $user = $this->userRepository->findOrCreateByGoogle(
            $payload['email'],
            $payload['name'] ?? $payload['email'],
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new TokenResource($user, $token), 'Login successful');
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = auth()->user();

        $this->userRepository->deleteCurrentToken($user);

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return $this->success(new UserResource(auth()->user()));
    }

    /**
     * @param \App\Http\Requests\Api\V1\Auth\ResendCodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Random\RandomException
     */
    public function resendCode(ResendCodeRequest $request): JsonResponse
    {
        $dto = ResendCodeDto::from($request->validated());

        $pending = $this->pendingRepository->findByEmail($dto->email);

        if (!$pending) {
            return $this->error('Registration request not found', 'not_found', 404);
        }

        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->pendingRepository->update($pending, [
            'otp_code'       => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::to($dto->email)->queue(new OtpCodeMail($otpCode));

        return $this->success(null, 'Verification code resent');
    }

    /**
     * @param ForgotPasswordRequest $request
     *
     * @return JsonResponse
     * @throws \Random\RandomException
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->input('email'));

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->userRepository->update($user, [
            'reset_otp_code'       => $otpCode,
            'reset_otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        Mail::to($user->email)->queue(new OtpCodeMail($otpCode));

        return $this->success(null, 'Password reset code sent to email');
    }

    /**
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->input('email'));

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        if ($user->reset_otp_code !== $request->input('code')) {
            return $this->error('Invalid code', 'invalid_code', 422);
        }

        if (!$user->reset_otp_expires_at || Carbon::parse($user->reset_otp_expires_at)->isPast()) {
            return $this->error('Code has expired', 'code_expired', 422);
        }

        $this->userRepository->update($user, [
            'password'             => Hash::make($request->input('password')),
            'reset_otp_code'       => null,
            'reset_otp_expires_at' => null,
        ]);

        return $this->success(null, 'Password reset successful');
    }
}
