<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Application\DTOs\Lead\LeadItemAddData;
use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AddCommentLeadUseCase
{

    public function __construct(private LeadRepositoryInterface $repository)
    {

    }
    public function execute(int $id, LeadItemAddData $dto, UserPermission $permission): void
    {
        $leadEntity = $this->repository->findById($id);

        $item =  new LeadItemEntity($dto->comment, $dto->staffId ?? $leadEntity->staffId);
        $item->finishedAt = $dto->finishedAt;
        $leadEntity->comment = $dto->comment; //Запоминаем последний коммент
        $leadEntity->addItem($item);
        $this->repository->save($leadEntity);
    }
}
