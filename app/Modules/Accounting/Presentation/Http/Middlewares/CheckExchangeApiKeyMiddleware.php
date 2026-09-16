<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Presentation\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckExchangeApiKeyMiddleware — проверка доступа к ExchangeController.
 *
 * Сравнивает заголовок X-API-Key со значением из env 1C_API_KEY.
 * При отсутствии или несовпадении ключа возвращает 401.
 */
final class CheckExchangeApiKeyMiddleware
{
    private const string HEADER_NAME = 'X-API-Key';

    public function handle(Request $request, Closure $next): Response
    {
        $expectedApiKey = (string) env('1С_API_KEY');
        $providedApiKey = (string) $request->header(self::HEADER_NAME);

        if ($expectedApiKey === '' || $providedApiKey !== $expectedApiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
