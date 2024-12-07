<?php

namespace App\Exceptions;

use App\Events\ThrowableHasAppeared;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Inertia\Inertia;
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

        $response = parent::render($request, $e);
        if (request()->is('admin/*')) {
            if (/*!app()->environment('local') && */ in_array($response->status(), [500, 503, 404, 403, 419])) {
                if ($response->status() == 403) return redirect()->back()->with('error', 'Отказано в доступе');
                if ($response->status() > 500) {
                    if (config('app.debug')) {
                        return $response;
                    } else {
                        event(new ThrowableHasAppeared($e));
                        return redirect()->back()->with('error', 'Непредвиденная ошибка');
                    }
                }

                if ($response->status() == 404) return Inertia::render('Error', ['status' => 404])
                    ->toResponse($request)
                    ->setStatusCode($response->status());
                if ($response->status() == 419 )return redirect()->back()->with('warning', 'Срок действия страницы истек, попробуйте еще раз.');
            }
        } else {
            if ($response->status() == 404) return response()->view('errors.' . '404', [], 404);
            //TODO Добавить все ошибки http
        }


        //Исключение CRM
        if ($e instanceof \DomainException) {
            if ($request->ajax()) {
                return \response()->json(['error' => $e->getMessage()]);
            } else {

                if (request()->is('admin/*')) { //Админ панель
                    flash($e->getMessage(), 'danger');
                    return redirect()->back()->with('error', $e->getMessage());
                } else { //Клиентская часть
                    return redirect()->back()->with('danger', $e->getMessage()); //route('shop.home')
                }
            }
        }
        return $response;
    }
}
