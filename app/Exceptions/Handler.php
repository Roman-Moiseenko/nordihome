<?php

namespace App\Exceptions;

use App\Events\ThrowableHasAppeared;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
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
            //TODO Добавить все ошибки http
            if (request()->is('admin/*')) {
                if ($e->getStatusCode() == 404)
                    return response()->view('errors.' . 'admin_404', [], 404);
            } else {
                if ($e->getStatusCode() == 404)
                    return response()->view('errors.' . '404', [], 404);
            }
        }

        //Исключение CRM
        if ($e instanceof \DomainException) {
            if ($request->ajax()) {
                return \response()->json(['error' => $e->getMessage()]);
            } else {

                if (request()->is('admin/*')) { //Админ панель
                    flash($e->getMessage(), 'danger');
                    return redirect()->back();
                } else { //Клиентская часть
                    return redirect()->back()->with('danger', $e->getMessage()); //route('shop.home')
                }
            }
        }
        if (!($e instanceof TokenMismatchException) && !($e instanceof AuthenticationException)) {
            if (config('app.debug')) {
                if ($request->ajax())
                    return \response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
            } else {
                //Если режим не Debug отправляем сообщения

                    event(new ThrowableHasAppeared($e));
                flash('Непредвиденная ошибка. Мы уже работаем над ее исправлением', 'danger');
                return redirect()->back();
            }
        }
        return parent::render($request, $e);
    }
}
