<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return Response::json([
                    'success'   => false,
                    'message'   =>  "Not Found.",
                ], HttpResponse::HTTP_NOT_FOUND);
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return Response::json([
                    'success'   => false,
                    'message'   => "Unauthenticated.",
                ], HttpResponse::HTTP_UNAUTHORIZED);
            }
        });

        $this->renderable(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                return Response::json([
                    'success'   => false,
                    'message'   => "Unauthenticated.",
                ], HttpResponse::HTTP_UNAUTHORIZED);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return Response::json([
                    'success'   => false,
                    'message'   =>  "Method Not Allowed.",
                ], HttpResponse::HTTP_METHOD_NOT_ALLOWED);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
