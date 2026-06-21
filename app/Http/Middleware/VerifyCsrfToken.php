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
        'api/telegram/web-hook/',
        'admin/mail/system/attachment',
        'file-upload',
        'admin/staff/photo/*',
        'admin/accounting/bank/upload',
        'admin/product/upload',
        'csrf-token',
        'product/search',
        'admin/*',
    ];

    public function handle($request, \Closure $next)
    {
        if ($request->header('X-Inertia')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
