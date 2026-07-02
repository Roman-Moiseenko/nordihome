<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'admin/*',
        'admin',
        'admin/',
        'api/telegram/web-hook/',
        'file-upload',
        'csrf-token',
        'product/search',
    ];

    public function handle($request, \Closure $next)
    {
        if ($request->header('X-Inertia')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
