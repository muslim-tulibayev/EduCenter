<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    }

    /**
     * Handle "Route Not Found" errors (my)
     */
    public function render($request, Throwable $exception)
    {
        //! fix this : set (lan) locale
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                "success" => false,
                "status" => 404,
                "message" => trans('msg.not_found', ['attribute' => __('msg.attributes.route')]),
                "data" => null,
                "pagination" => null,
            ]);
        }

        return parent::render($request, $exception);
    }
}
