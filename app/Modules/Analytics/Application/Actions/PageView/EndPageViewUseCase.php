<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\PageView;

use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;
use DateTimeImmutable;

/**
 * EndPageView — завершение просмотра страницы.
 *
 * Проставляет длительность пребывания и глубину прокрутки. Если просмотр
 * оказался последним в сессии (выход) — дополнительно помечает его как exit.
 */
final class EndPageViewUseCase
{
    public function __construct(
        private readonly PageViewRepositoryInterface $pageViews,
    ) {}

    public function execute(
        int $pageViewId,
        int $duration,
        ?int $scrollDepth = null,
        bool $isExit = false,
    ): void {
        $this->pageViews->finalizeView($pageViewId, $duration, $scrollDepth, $isExit);

        if ($isExit) {
            $this->pageViews->markAsExit($pageViewId, new DateTimeImmutable());
        }
    }
}
