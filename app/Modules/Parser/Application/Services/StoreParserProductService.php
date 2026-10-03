<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Parser\Application\Actions\ParserLog\CreateParserLogUseCase;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Domain\ValueObjects\Store;

readonly class StoreParserProductService
{
    public function __construct(
        private LoadParserProductIkeaService     $service,
        private CreateParserLogUseCase           $createParserLogUseCase,
        private ParserProductRepositoryInterface $repository,


    )
    {
    }
    public function execute(int $productId): void
    {
        $productEntity = $this->repository->getById($productId);
        $result = $this->service->remainsProduct($productEntity);
        if (is_null($result)) {
            $dto = new ParserLogCreateData(
                status: ParserStatus::deleted(),
                parserId: $productId,
            );
            $this->createParserLogUseCase->execute($dto);
            $productEntity->availability = false;
            $this->repository->save($productEntity);
        } else {

            $productEntity->stores = Store::collectionFromAssociative($result);
            $this->repository->save($productEntity);
        }


    }
}
