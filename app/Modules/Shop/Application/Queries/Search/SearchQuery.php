<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Analytics\Application\Services\TrackSearchService;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Search\FullSearchData;

readonly class SearchQuery
{
    public function __construct(
        private CatalogSearchQuery       $catalogSearch,
        private ParserSearchQuery $parserSearch,
        private TrackSearchService       $trackSearchService,
    ) {}

    public function execute(string $search, ClientContext $client): FullSearchData
    {
        $data = $this->catalogSearch->execute($search, $client);
        //Учитываем только нулевой результат
        if (empty($data->products)) {
            $this->trackSearchService->execute($search, 0);
            //Ищем по артикулу в Парсере
            $data = $this->parserSearch->execute($search, $client);
        }

        return $data;
    }
}

