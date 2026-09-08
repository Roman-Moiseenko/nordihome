<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;


class ViewParserProductUseCase
{
    public function __construct(
        private readonly ParserProductRepositoryInterface $productRepository,
    ) {}


    public function execute(int $id, UserPermission $userPermission): ParserProductEntity
    {
        if (!$userPermission->can('parser.product.view'))
            throw new AccessDeniedException();


        return $this->productRepository->getById($id);

    }
}
