<?php

namespace App\Exceptions;

use App\Http\Traits\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Exception;

class Handler extends ExceptionHandler
{
    use Response;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
//        HttpException::class,
//        ModelNotFoundException::class,
//        TokenMismatchException::class,
//        ValidationException::class,
//        AuthenticationException::class,
//        AuthorizationException::class,
//        QueryException::class,
//        ThrottleRequestsException::class,
//        PostTooLargeException::class,
//        RouteNotFoundException::class
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
        $this->renderable(function (RouteNotFoundException $e, $request) {
            return $this->responseApi('Try to login,Invalid Token',null, 500);
        });

        $this->renderable(function (ModelNotFoundException $exception, $request) {
            return $this->responseApi('Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found', null, 404);
        });
        $this->renderable(function (AuthenticationException $exception, $request) {
            return $this->responseApi('Unauthenticated or Token Expired, Please Login again', null, 401);
        });

        $this->renderable(function (AuthorizationException $exception, $request) {
            return $this->responseApi('Not Authorized', null, 401);
        });

        $this->renderable(function (ValidationException $exception, $request) {
            return $this->responseApi($exception->validator->errors()->first(), $exception->errors(), 422);
        });

        $this->renderable(function (QueryException $exception, $request) {
            return $this->responseApi('There was Issue with the Query', null, 500);
        });

        $this->renderable(function (\Error $exception, $request) {
            return $this->responseApi("There was some internal error", null, 500);
        });

        $this->renderable(function (ThrottleRequestsException $exception, $request) {
            return $this->responseApi('Too Many Requests,Please Slow Down', null, 429);
        });

        $this->renderable(function (PostTooLargeException $exception, $request) {
            return $this->responseApi("Size of attached file should be less " . ini_get("upload_max_filesize") . "B", null, 400);
        });

        $this->renderable(function (NotFoundHttpException $exception, $request) {
            return $this->responseApi('Not Found', null, 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $exception, $request) {
            return $this->responseApi('Method not allowed', null, 405);
        });

        $this->renderable(function (NotAcceptableHttpException $exception, $request) {
            return $this->responseApi('The used HTTP Accept header is not allowed on this route.', null, $exception->getStatusCode());
        });

        $this->renderable(function (TokenMismatchException $exception, $request) {
            return $this->responseApi($exception->getMessage(), null, 419);
        });
    }
}
