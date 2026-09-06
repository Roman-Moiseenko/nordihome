<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveAdditionUseCase
{

    public function __construct(private AdditionRepositoryInterface $repository)
    {

    }

    public function execute(int $id, UserPermission $permission): void
    {
        if (!$permission->can('guide.guide.remove')) throw new AccessDeniedException();
        //TODO Проверка на наличие услуг

        $this->repository->remove($id);
    }
}
