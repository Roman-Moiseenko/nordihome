<?php

namespace App\Modules\Shop\Presentation\Http\Middlewares;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Closure;
use Illuminate\Http\Request;
class InjectClientContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Client $client */
        $client = (auth()->check() && auth()->user()->isClient()) ? auth()->user()->profileable : null;
        $user_ui = $request->cookie('user_cookie_id');
        $context = new ClientContext(
            id: $client->id ?? null,
            uuid: $user_ui,
            priceType: $client?->getPriceType() ?? PriceType::retail(),
        );

        $request->attributes->set('client_context', $context);

        return $next($request);
    }
}
