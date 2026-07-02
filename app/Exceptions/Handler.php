<?php

namespace App\Exceptions;

use App\Events\ThrowableHasAppeared;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Throwable;
use Illuminate\Support\Facades\Config;

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
        // Логируем все 419 ошибки для отладки
        if ($e instanceof TokenMismatchException) {
            Log::error('419_CSRF_MISMATCH', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ajax' => $request->ajax(),
                'inertia' => $request->inertia(),
                'expects_json' => $request->expectsJson(),
                'has_session' => $request->hasSession(),
                'session_token' => $request->hasSession() ? $request->session()->token() : 'no-session',
                'header_xsrf' => $request->header('X-XSRF-TOKEN', 'missing'),
                'header_csrf' => $request->header('X-CSRF-TOKEN', 'missing'),
                'cookie_xsrf' => $request->cookie('XSRF-TOKEN', 'missing'),
                'input_token' => $request->input('_token', 'missing'),
                'is_admin' => $request->is('admin/*'),
            ]);
        }

        // Логируем любой 419 статус
        $statusCode = 0;
        try {
            $statusCode = parent::render($request, $e)->getStatusCode();
        } catch (\Throwable $renderError) {
            Log::error('419_RENDER_ERROR', ['error' => $renderError->getMessage()]);
        }

        if ($statusCode === 419 && !($e instanceof TokenMismatchException)) {
            Log::error('419_NON_CSRF', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'status' => $statusCode,
            ]);
        }

        if ($e instanceof \DomainException || $e instanceof \InvalidArgumentException) {
            if ($request->inertia()) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            if ($request->ajax() || $request->expectsJson()) {
                return \response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'type' => 'error']);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        $shop_errors = 'shop.errors.';

        $response = parent::render($request, $e);


        if (request()->is('admin/*')) {//Админ панель
            if (in_array($response->status(), [500, 503, 404, 403, 419])) {
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
                if ($response->status() == 419) return redirect()->back()->with('warning', 'Срок действия страницы истек, попробуйте еще раз.');
            }
            //Ошибки при работе с базой данный
            if ($e instanceof \Illuminate\Database\QueryException) {
                if ($e->errorInfo[1] == 1062) {
                    return redirect()->back()->with('error', 'Дубликат записи');
                } else {

                }

            }
        } else {
            //Клиентская часть
            if ($response->status() == 404) return response()->view($shop_errors . '404', [], 404);
            if ($response->status() == 500) {
                //TODO отправка event(new ThrowableHasAppeared($e));
                Log::error('Ошибка 500 ' . json_encode([$e->getMessage(), $e->getFile(), $e->getLine()]));
                return response()->view($shop_errors . '500', [], 500);


            }
            //TODO Добавить все ошибки http
        }



        // Исключение CRM - дублируем на случай, если не сработал блок в начале
        // (например, для не-Inertia запросов)
        if ($e instanceof \DomainException || $e instanceof \InvalidArgumentException) {
            if ($request->inertia()) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            if ($request->ajax()) {
                return \response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'type' => 'error']);
            } else {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        if ($e instanceof ValidationException) {
            if ($request->inertia()) {
                return redirect()->back()->withErrors($e->errors())->withInput();
            }
            return $response;
        }
//TODO
        /*
         *  if ($e instanceof \Throwable) {
             event(new ThrowableHasAppeared($e));
             return redirect()->back()->with('error', 'Непредвиденная ошибка');
 }
         */
        return $response;
    }
}
