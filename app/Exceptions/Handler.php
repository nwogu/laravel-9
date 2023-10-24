<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json(["message" => "Resource not found"], 404);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(["message" => "Resource not found"], 404);
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json(["message" => $e->getMessage()], $e->getStatusCode());
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json(["message" => "Authentication failed"], 401);
        });
    }
}
