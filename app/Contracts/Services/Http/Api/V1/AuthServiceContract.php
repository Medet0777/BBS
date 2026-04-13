<?php

namespace App\Contracts\Services\Http\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\ResendCodeRequest;

interface AuthServiceContract
{

    /**
     * @param \App\Http\Requests\Api\V1\Auth\RegisterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse;

    /**
     * @param \App\Http\Requests\Api\V1\Auth\VerifyEmailRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse;

    /**
     * @param \App\Http\Requests\Api\V1\Auth\LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse;

    /**
     * @param \App\Http\Requests\Api\V1\Auth\GoogleLoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): JsonResponse;

    /**
     * @param \App\Http\Requests\Api\V1\Auth\ResendCodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendCode(ResendCodeRequest $request): JsonResponse;
}