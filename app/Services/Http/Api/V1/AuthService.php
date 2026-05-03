<?php

namespace App\Services\Http\Api\V1;

use App\Contracts\Repositories\PendingRegistrationRepositoryContract;
use App\Contracts\Repositories\ReviewRepositoryContract;
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
use App\Http\Requests\Api\V1\Auth\UpdateMeRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Http\Resources\Api\V1\Auth\TokenResource;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Traits\Services\Http\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Mail\OtpCodeMail;
use App\Services\Mail\BrevoMailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
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
        private readonly ReviewRepositoryContract              $reviewRepository,
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

        if ($this->userRepository->findByEmail($dto->email)) {
            return $this->error('Email already registered', 'email_taken', 422);
        }

        $user = $this->userRepository->create([
            'name'              => $dto->name,
            'email'             => $dto->email,
            'password'          => Hash::make($dto->password),
            'email_verified_at' => Carbon::now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new TokenResource($user, $token), 'Registration successful', 201);
    }

    /**
     * @param VerifyEmailRequest $request
     *
     * @return JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->input('email'));

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new TokenResource($user, $token), 'Verified', 200);
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

        return $this->success(null, 'Password reset allowed');
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

        $this->userRepository->update($user, [
            'password'             => Hash::make($request->input('password')),
            'reset_otp_code'       => null,
            'reset_otp_expires_at' => null,
        ]);

        return $this->success(null, 'Password reset successful');
    }

    /**
     * @param UpdateMeRequest $request
     *
     * @return JsonResponse
     */
    public function updateMe(UpdateMeRequest $request): JsonResponse
    {
        $user = auth()->user();

        $data = array_filter([
            'name'  => $request->input('name'),
            'phone' => $request->input('phone'),
        ], fn ($value) => $value !== null);

        if (empty($data)) {
            return $this->success(new UserResource($user), 'Nothing to update');
        }

        $user = $this->userRepository->update($user, $data);

        return $this->success(new UserResource($user), 'Profile updated');
    }

    /**
     * @return JsonResponse
     */
    public function myReviews(): JsonResponse
    {
        $reviews = $this->reviewRepository->getUserReviewsPaginated(auth()->id(), 15);

        $data = $reviews->getCollection()->map(fn ($review) => [
            'id'                => $review->id,
            'barbershop_name'   => $review->barbershop?->name,
            'barbershop_slug'   => $review->barbershop?->slug,
            'barbershop_image'  => $review->barbershop?->logo,
            'rating'            => $review->rating,
            'comment'           => $review->comment,
            'created_at'        => $review->created_at,
        ]);

        return $this->success([
            'data'         => $data,
            'current_page' => $reviews->currentPage(),
            'last_page'    => $reviews->lastPage(),
            'per_page'     => $reviews->perPage(),
            'total'        => $reviews->total(),
        ]);
    }

    /**
     * @param string $email
     * @param string $code
     *
     * @return void
     */
    private function sendOtpEmail(string $email, string $code): void
    {
        $html = View::make('mail.otp-code', ['otpCode' => $code])->render();

        app(BrevoMailService::class)->send($email, '', 'BarberHub Verification Code', $html);
    }
}
