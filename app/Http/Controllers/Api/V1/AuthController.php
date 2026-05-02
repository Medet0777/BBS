<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\Http\Api\V1\AuthServiceContract;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResendCodeRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\UpdateMeRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{

    /**
     * @param RegisterRequest     $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/auth/register',
        operationId: 'authRegister',
        description: 'Creates a pending registration and sends OTP code to email',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123', minLength: 8),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Verification code sent',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Verification code sent to email'),
                    new OA\Property(property: 'data', type: 'string', example: null, nullable: true),
                ])
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                    new OA\Property(property: 'error', type: 'string', nullable: true),
                ])
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/auth/verify-email',
        operationId: 'authVerifyEmail',
        description: 'Verifies OTP code, creates user account and returns auth token',
        summary: 'Verify email with OTP code',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'code'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'code', type: 'string', example: '1234', maxLength: 4, minLength: 4),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registration successful',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Registration successful'),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'user', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                            new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', example: '2026-04-03T12:00:00.000000Z'),
                        ], type: 'object'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid or expired code',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Invalid or expired code'),
                    new OA\Property(property: 'error', type: 'string', example: 'invalid_otp'),
                ])
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/auth/login',
        operationId: 'authLogin',
        description: 'Authenticates user and returns auth token',
        summary: 'Login with email and password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'user', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                            new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', example: '2026-04-03T12:00:00.000000Z'),
                        ], type: 'object'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Invalid email or password'),
                    new OA\Property(property: 'error', type: 'string', example: 'invalid_credentials'),
                ])
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/auth/google',
        operationId: 'authGoogleLogin',
        description: 'Authenticates user via Google id_token',
        summary: 'Login with Google',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_token'],
                properties: [
                    new OA\Property(property: 'id_token', description: 'Google OAuth2 id_token', type: 'string', example: 'eyJhbGciOiJSUzI1NiIs...'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'user', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                            new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time'),
                        ], type: 'object'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid Google token',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Invalid Google token'),
                    new OA\Property(property: 'error', type: 'string', example: 'invalid_google_token'),
                ])
            ),
        ]
    )]
    public function googleLogin(GoogleLoginRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->googleLogin($request);
    }

    /**
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/auth/logout',
        operationId: 'authLogout',
        description: 'Revokes current access token',
        summary: 'Logout',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged out successfully',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
                    new OA\Property(property: 'data', type: 'string', example: null, nullable: true),
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated'),
                ])
            ),
        ]
    )]
    public function logout(AuthServiceContract $service): JsonResponse
    {
        return $service->logout();
    }

    /**
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/auth/me',
        operationId: 'authMe',
        description: 'Returns authenticated user data',
        summary: 'Get current user',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current user data',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: null, nullable: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', example: '2026-04-03T12:00:00.000000Z'),
                    ], type: 'object'),
                ])
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated'),
                ])
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/auth/resend-code',
        operationId: 'authResendCode',
        description: 'Generates new OTP code and sends it to email',
        summary: 'Resend OTP code',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Code resent',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Verification code resent'),
                    new OA\Property(property: 'data', type: 'string', example: null, nullable: true),
                ])
            ),
            new OA\Response(
                response: 404,
                description: 'Registration request not found',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Registration request not found'),
                    new OA\Property(property: 'error', type: 'string', example: 'not_found'),
                ])
            ),
        ]
    )]
    public function resendCode(ResendCodeRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->resendCode($request);
    }

    /**
     * @param ForgotPasswordRequest $request
     * @param AuthServiceContract   $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/auth/forgot-password',
        operationId: 'authForgotPassword',
        description: 'Sends password reset OTP code to email',
        summary: 'Forgot password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Reset code sent'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function forgotPassword(ForgotPasswordRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->forgotPassword($request);
    }

    /**
     * @param ResetPasswordRequest $request
     * @param AuthServiceContract  $service
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/auth/reset-password',
        operationId: 'authResetPassword',
        description: 'Resets password using OTP code sent to email',
        summary: 'Reset password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'code', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'code', type: 'string', example: '1234', maxLength: 4, minLength: 4),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Password reset'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Invalid code or expired'),
        ]
    )]
    public function resetPassword(ResetPasswordRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->resetPassword($request);
    }

    /**
     * @param UpdateMeRequest     $request
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Put(
        path: '/auth/me',
        operationId: 'authUpdateMe',
        description: 'Updates name and/or phone of the authenticated user. Email is not editable.',
        summary: 'Update my profile',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Yeskendir'),
                    new OA\Property(property: 'phone', type: 'string', example: '+7 701 234 5678'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function updateMe(UpdateMeRequest $request, AuthServiceContract $service): JsonResponse
    {
        return $service->updateMe($request);
    }

    /**
     * @param AuthServiceContract $service
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/auth/me/reviews',
        operationId: 'authMyReviews',
        description: 'Returns paginated list of reviews left by the authenticated user',
        summary: 'My reviews',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Reviews list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function myReviews(AuthServiceContract $service): JsonResponse
    {
        return $service->myReviews();
    }
}
