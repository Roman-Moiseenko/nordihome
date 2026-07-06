<?php

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

class ViewParserCategoryUseCase
{

    public function __construct(private ParserCategoryRepositoryInterface $categoryRepository)
    {
    }

    public function execute(int $id, UserPermission $userPermission): ParserCategoryEntity
    {
        if (!$userPermission->can('parser.category.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        return $this->categoryRepository->getById($id);
    }
}
