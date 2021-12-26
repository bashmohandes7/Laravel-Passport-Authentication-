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
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use Response;
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

    protected function invalidJson($request, ValidationException $exception) {
        return $this->responseApi($exception->getMessage(), $exception->errors(), 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception) {
        if ($request->expectsJson()) {
            return $this->responseApi('Unauthenticated', null, 401);
        }
    }

    public function render($request,\Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof ModelNotFoundException) {
                return $this->responseApi('Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found',null, 404);
            }
            if ($exception instanceof AuthenticationException) {
                return $this->responseApi('Unauthenticated or Token Expired, Please Login',null,401);
            }
            if ($exception instanceof AuthorizationException) {
                return $this->responseApi('Not Authorized',null,401);
            }
            if ($exception instanceof ValidationException) {
                return $this->responseApi($exception->getMessage(),$exception->errors(),422);
            }
            if ($exception instanceof QueryException) {

                return $this->responseApi('There was Issue with the Query',null,500);
            }
            if ($exception instanceof \Error) {
                return $this->responseApi("There was some internal error", null,500);
            }
            if ($exception instanceof ThrottleRequestsException) {
                return $this->responseApi('Too Many Requests,Please Slow Down', null, 429);
            }
            if ($exception instanceof PostTooLargeException) {
                return $this->responseApi("Size of attached file should be less " . ini_get("upload_max_filesize") . "B", null,400);
            }
            return parent::render($request, $exception);
        }
    }

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
    }
}
