<?php

namespace App\Modules\Storefront\Presentation\Http\Middlewares;
use App\Modules\Storefront\Application\DTOs\ClientContext;
use App\Modules\Storefront\Application\Services\ClientContextFactory;
use Closure;
use Illuminate\Http\Request;

class InjectClientContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {

    /*    $client = (auth()->check() && auth()->user()->isClient()) ? auth()->user()->profileable : null;
        $user_ui = $request->cookie('user_cookie_id');
        $context = new ClientContext(
            id: $client->id ?? null,
            uuid: $user_ui,
            priceType: $client?->getPriceType() ?? PriceType::retail(),
        );
        app()->instance(ClientContext::class, $context);
        $request->attributes->set('client_context', $context);
*/

        $context = app(ClientContextFactory::class)->make();
        app()->instance(ClientContext::class, $context);
        $request->attributes->set('client_context', $context);

        //\Log::info('InjectClientContextMiddleware');
        return $next($request);
    }
}
