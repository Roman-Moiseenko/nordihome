<?php

namespace App\Modules\Analytics\Application\Services;

use App\Modules\Analytics\Application\Actions\Search\TrackSearchUseCase;
use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;

/**
 * Учет поиска для бекенда
 */
readonly class TrackSearchService
{

    public function __construct(
        private TrackSearchUseCase      $trackSearch,
        private VisitorContextInterface $analyticsContext,
    )
    {
    }

    public function execute(string $query, int $resultsCount)
    {
        $visitorId = $this->analyticsContext->getVisitorId();
        if ($visitorId !== null) {

            $this->trackSearch->execute(
                $visitorId,
                $this->analyticsContext->getSessionId(),
                $this->analyticsContext->getPageViewId(),
                $query,
                $resultsCount,
            );
        }
    }
}
