<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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

    public function render($request, Throwable $e)
    {
        $messages = config('order_messages.exceptions');

        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $messages['session_expired_json'],
                ], 419);
            }

            if ($request->is('login') || $request->is('logout')) {
                return redirect()
                    ->route('login')
                    ->withErrors(['email' => $messages['session_expired_login']]);
            }

            return redirect()
                ->back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['session' => $messages['session_expired_back']]);
        }

        if ($e instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $messages['unauthenticated']], 401);
            }

            return redirect()->route('login')->withErrors([
                'email' => $messages['signin_first'],
            ]);
        }

        if ($e instanceof AuthorizationException) {
            return $this->commonErrorResponse($request, $messages['forbidden'], 403);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $this->commonErrorResponse($request, $messages['not_found'], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->commonErrorResponse($request, $messages['method_not_allowed'], 405);
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->commonErrorResponse($request, $messages['too_many_requests'], 429);
        }

        if ($e instanceof ValidationException) {
            if ($request->routeIs('orders.store') && $e->errors() && array_key_exists('table_id', $e->errors())) {
                $message = config('order_messages.validation.table_required');

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'errors' => [
                            'table_id' => [$message],
                        ],
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors(['table_id' => $message]);
            }

            return parent::render($request, $e);
        }

        return parent::render($request, $e);
    }

    private function commonErrorResponse($request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return response()->view('errors.common', [
            'message' => $message,
            'status' => $status,
        ], $status);
    }
}
