<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\AuthServiceContract;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResendCodeRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    /**
     * @param RegisterRequest     $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->register($request);
    }

    /**
     * @param VerifyEmailRequest  $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->verifyEmail($request);
    }

    /**
     * @param LoginRequest        $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->login($request);
    }

    /**
     * @param GoogleLoginRequest  $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function googleLogin(GoogleLoginRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->googleLogin($request);
    }

    /**
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function logout(AuthServiceContract $service): JsonResponse
    {
        return $service->logout();
    }

    /**
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function me(AuthServiceContract $service): JsonResponse
    {
        return $service->me();
    }

    /**
     * @param ResendCodeRequest   $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    public function resendCode(ResendCodeRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->resendCode($request);
    }
}
