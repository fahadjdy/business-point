<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    /**
     * Standard success response.
     */
    protected function success($data = null, string $message = '', int $code = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $code);
    }

    /**
     * Standard error response.
     */
    protected function error(string $message, array $errors = [], int $code = 422): JsonResponse
    {
        return ApiResponse::error($message, $errors, $code);
    }
}
