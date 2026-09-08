<?php

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Parser\Domain\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

class ViewParserCategoryUseCase
{

    public function __construct(private ParserCategoryRepositoryInterface $categoryRepository)
    {
    }

    public function execute(int $id, UserPermission $userPermission): ParserCategoryEntity
    {
        if (!$userPermission->can('parser.category.view')) {
            throw new AccessDeniedException();
        }

        return $this->categoryRepository->getById($id);
    }
}
