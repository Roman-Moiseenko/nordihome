<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Http\Middlewares;

use App\Modules\Analytics\Infrastructure\Jobs\LinkVisitorToClientJob;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LinkVisitorToClientMiddleware — привязка visitor к client_id при логине.
 *
 * Если в снимке есть clientId (авторизованный клиент), отправляет задачу
 * канонизации/привязки в очередь analytics. Не блокирует запрос.
 */
final class LinkVisitorToClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $snapshot = $request->attributes->get('analytics_snapshot');

        if ($snapshot === null || $snapshot->clientId === null) {
            return $next($request);
        }

        dispatch(new LinkVisitorToClientJob(
            uuid: (string) $snapshot->uuid,
            clientId: $snapshot->clientId,
        ))->onQueue(QueueName::ANALYTICS);

        return $next($request);
    }
}
