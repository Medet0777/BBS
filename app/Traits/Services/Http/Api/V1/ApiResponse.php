<?php

namespace App\Traits\Services\Http\Api\V1;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{

    /**
     * @param mixed|null  $data
     * @param string|null $message
     * @param int         $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(mixed $data = null, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data'    => $data,
            ],
            $code
        );
    }

    /**
     * @param string      $message
     * @param string|null $error
     * @param int         $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message, string $error = null, int $code = 400): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'message' => $message,
                'error'   => $error,
            ],
            $code
        );
    }
}
