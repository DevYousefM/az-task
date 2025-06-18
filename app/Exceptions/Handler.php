<?php

namespace App\Exceptions;

use App\Classes\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Handle API exceptions
     */
    private function handleApiException(Throwable $e)
    {
        if ($e instanceof ProductNotFoundException) {
            return ApiResponse::error([], $e->getMessage(), $e->getCode());
        }

        if ($e instanceof ValidationException) {
            return ApiResponse::error($e->errors(), 'Validation failed', 422);
        }

        if ($e instanceof AuthenticationException) {
            return ApiResponse::error([], 'Unauthenticated', 401);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return ApiResponse::error([], 'Resource not found', 404);
        }

        // For other exceptions, return generic error in production
        if (app()->environment('production')) {
            return ApiResponse::error([], 'Internal server error', 500);
        }

        // In development, return the actual error
        return ApiResponse::error([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 'Internal server error', 500);
    }
} 