<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\ViewComposers;

use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;
use App\Modules\Analytics\Infrastructure\Services\VisitorUuidGenerator;
use Illuminate\View\View;

/**
 * AnalyticsComposer — передаёт в шаблоны данные для JS-трекера аналитики.
 *
 * - analyticsUuid      — UUID посетителя из cookie (user_cookie_id);
 * - analyticsPageViewId — id текущего page_view из scoped-контекста.
 */
final class AnalyticsComposer
{
    public function __construct(
        private readonly VisitorContextInterface $context,
    ) {}

    public function compose(View $view): void
    {
        $view->with('analyticsUuid', request()->cookie(VisitorUuidGenerator::COOKIE_NAME));
        $view->with('analyticsPageViewId', $this->context->getPageViewId());
    }
}
