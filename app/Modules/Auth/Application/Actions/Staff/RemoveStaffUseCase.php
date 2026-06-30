<?php

namespace App\Modules\Auth\Application\Actions\Staff;

use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class RemoveStaffUseCase
{
    public function __construct(
        private StaffRepositoryInterface $staffRepository
    )
    {
    }

    public function execute(int $id, UserPermission $permissions): bool
    {
        if (!$permissions->can('auth.employee.delete')) throw new AccessDeniedException();

        if (!$this->staffRepository->findById($id))
            throw new NotFoundHttpException('Сотрудник не найден');
        //Проверка, можем ли удалить

        return $this->staffRepository->delete($id);
    }
}
