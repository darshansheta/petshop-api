<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
    }

    public function render($request, Throwable $e)
    {
        return match (true) {
            $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
            default => parent::render($request, $e),
        };
    }

    protected function convertValidationExceptionToResponse(ValidationException $exception, $request)
    {
        return response()->json([
            'error' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    protected function shouldReturnJson($request, Throwable $e)
    {
        return true;
    }
}
