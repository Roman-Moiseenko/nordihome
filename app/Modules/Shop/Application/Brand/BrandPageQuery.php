<?php

namespace App\Modules\Shop\Application\Brand;

use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Pages\ProductIndexPageData;
use App\Modules\Shop\Application\Services\RegionalPriceCalculator;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContentBlockQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomPageQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

readonly class BrandPageQuery
{
    public function __construct(
        private BrandPageQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        private SeoAdapter                  $seoAdapter,
        private ProductIndexQueryRepository $productIndexQueryRepository,
        private AttributeQueryRepository    $attributeQueryRepository,
        private SchemaBuilder               $schemaBuilder,
        private ContentBlockQueryRepository   $blockRepository,
        private RoomPageQueryRepository     $roomRepository,
        private GetWebSettingsUseCase $webSettingsUseCase,
        private RegionalPriceCalculator $regionalPriceCalculator,
    ) {}

    public function execute(string $slug, array $params, ClientContext $clientContext): ?ProductIndexPageData
    {
        $web = $this->webSettingsUseCase->execute();
        $mainInfo = $this->repository->getBrand($slug);
        if (is_null($mainInfo)) return null;
    }
}
