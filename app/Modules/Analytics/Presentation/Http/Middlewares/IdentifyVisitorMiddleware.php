<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Http\Middlewares;

use App\Modules\Analytics\Application\DTOs\VisitSnapshot;
use App\Modules\Analytics\Domain\ValueObjects\UtmData;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use App\Modules\Analytics\Infrastructure\Services\UserAgentParser;
use App\Modules\Analytics\Infrastructure\Services\VisitorUuidGenerator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * IdentifyVisitorMiddleware — идентификация посетителя на каждом запросе.
 *
 * Не обращается к БД: формирует VisitSnapshot (uuid, client_id, ip, user-agent,
 * referrer, utm, device) и кладёт его в request-атрибуты. Если cookie uuid нет —
 * генерирует новый и помечает ответ для установки cookie. Пропускает /admin/*,
 * /api/analytics/* и служебные маршруты.
 */
final class IdentifyVisitorMiddleware
{
    public function __construct(
        private readonly VisitorUuidGenerator $uuidGenerator,
        private readonly UserAgentParser $userAgentParser,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $cookieName = VisitorUuidGenerator::COOKIE_NAME;

        $uuid = $request->cookie($cookieName);
        $isNewUuid = false;

        if (empty($uuid)) {
            $uuid = (string) $this->uuidGenerator->generate();
            $isNewUuid = true;
        }

        $user = Auth::user();
        $clientId = ($user !== null && $user->isClient()) ? (int) $user->profileable_id : null;

        $userAgent = (string) $request->userAgent();

        $snapshot = new VisitSnapshot(
            uuid: new VisitorUuid($uuid),
            clientId: $clientId,
            ip: (string) $request->ip(),
            userAgent: $userAgent,
            referrer: $request->headers->get('referer'),
            utm: UtmData::fromQuery($request->query()),
            device: $this->userAgentParser->parse($userAgent),
            isBot: $this->userAgentParser->isBot($userAgent),
            url: $request->fullUrl(),
            isNewUuid: $isNewUuid,
        );

        $request->attributes->set('analytics_snapshot', $snapshot);

        $response = $next($request);

        if ($isNewUuid) {
            $response->headers->setCookie(
                cookie(
                    $cookieName,
                    $uuid,
                    VisitorUuidGenerator::COOKIE_MINUTES,
                    '/',
                    null,
                    false,
                    false,
                )
            );
        }

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is('analytics/*')
            || $request->is('admin/*')
            || $request->is('feed/*')
            || $request->is('up', 'sitemap.xml', 'robots.txt');
    }
}
