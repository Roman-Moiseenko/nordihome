<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class IndexParserCategoryUseCase
{
    public function __construct(
        private ParserCategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * @return ParserCategoryEntity[]
     */
    public function execute(UserPermission $userPermission): array
    {
        //if (!$userPermission->can('parser.category.view')) throw new \DomainException('Доступ запрещён');


        return $this->categoryRepository->getTree();
    }
}
