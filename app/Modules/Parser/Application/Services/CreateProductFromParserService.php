<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateProductFromParserService
{

    public function __construct(
        public ParserProductRepositoryInterface $parserProductRepository,
    )
    {

    }

    public function execute(int $id, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.create')) throw new \DomainException('Отсутствует доступ');

        $parserEntity = $this->parserProductRepository->getById($id);

        //MAINDO Создать

        // DTO ProductCreate

        // UseCase ProductCreate

        //Сохраняем id product для $parserEntity

        return new ProductEntity('', new Code(''), new Slug(''), 1, 1);
    }
}
