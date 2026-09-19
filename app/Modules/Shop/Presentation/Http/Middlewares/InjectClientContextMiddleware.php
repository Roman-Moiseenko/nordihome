<?php

namespace App\Modules\Shop\Presentation\Http\Middlewares;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\Services\ClientContextFactory;
use Closure;
use Illuminate\Http\Request;

class InjectClientContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $context = app(ClientContextFactory::class)->make();
        app()->instance(ClientContext::class, $context);
        $request->attributes->set('client_context', $context);
        return $next($request);
    }
}
