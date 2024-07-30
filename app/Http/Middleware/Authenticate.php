<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {

        if (request()->is('admin*')) {
            return $request->expectsJson() ? null : route('admin.login');
        } else {
            //dd(route('shop.home'));
            //dd(redirect()->route('shop.home')->with('danger', 'Доступ ограничен, Вам необходима аутентификация'));
            Session::flash('warning', 'Доступ ограничен, Вам необходима аутентификация');
            return $request->expectsJson() ? null : route('shop.home');
        }

    }
}
