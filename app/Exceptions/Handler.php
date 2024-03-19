<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest('/admin/login');
        }

        return redirect()->guest(route('login'));
    }
    public function render($request, Throwable $e)
    {

        if ($this->isHttpException($e)) {

            if (request()->is('admin/*')) {
                if ($e->getStatusCode() == 404) {
                    return response()->view('errors.' . 'admin_404', [], 404);
                }
            }
            else
            {
                if ($e->getStatusCode() == 404) {
                    return response()->view('errors.' . '404', [], 404);
                }
            }


        }
        return parent::render($request, $e);
    }
}
