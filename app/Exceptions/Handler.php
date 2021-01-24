<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json([
                'message' => 'Model not found.',
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json([
                'message' => 'Endpoint not found.',
                'status' => 'error',
                'code' => JsonResponse::HTTP_NOT_FOUND,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($exception instanceof AuthenticationException && $request->expectsJson()) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => 'error',
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof HttpException && $request->wantsJson()) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => 'error',
                'code' => $exception->getStatusCode(),
            ], $exception->getStatusCode());
        }

//        if ($exception instanceof MethodNotAllowedHttpException) {
//            abort(JsonResponse::HTTP_METHOD_NOT_ALLOWED, 'Method not allowed');
//        }
//
        if ($request->isJson() && $exception instanceof ValidationException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => 'error',
                'code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $exception->validator->getMessageBag()->toArray(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return parent::render($request, $exception);
    }
}
