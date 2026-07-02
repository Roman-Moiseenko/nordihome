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

        // Проверяем, почему не срабатывает except для admin/catalog/upload
        if ($request->is('admin/*') && !$request->is('admin/test-except')) {
            \Illuminate\Support\Facades\Log::warning('CSRF_ADMIN_EXCEPT_CHECK', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'in_except' => $this->inExceptArray($request),
                'is_reading' => $this->isReading($request),
                'path' => $request->decodedPath(),
                'except_list' => $this->getExcludedPaths(),
            ]);
        }

        return parent::handle($request, $next);
    }
}
