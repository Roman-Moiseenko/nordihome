<?php
declare(strict_types=1);

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ShopCookieUserIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user_ui = Cookie::get('user_cookie_id');
        if (empty($user_ui)) $user_ui = Str::uuid();
        return $next($request)->withCookie(\cookie()->make('user_cookie_id', $user_ui));
    }
}
