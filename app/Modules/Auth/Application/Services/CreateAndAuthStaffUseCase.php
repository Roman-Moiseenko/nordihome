<?php

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\Actions\Staff\CreateStaffUseCase;
use App\Modules\Auth\Application\Actions\User\RegisterStaffUserUseCase;
use App\Modules\Auth\Application\DTOs\Staff\StaffCreateData;
use App\Modules\Auth\Application\DTOs\User\UpdateUserData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use http\Exception\InvalidArgumentException;

class CreateAndAuthStaffUseCase
{

    public function __construct(
        private readonly CreateStaffUseCase $createStaffUseCase,
        private readonly RegisterStaffUserUseCase $registerStaffUserUseCase,
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function execute(StaffCreateData $dto, ?UserPermission $permissions)
    {
        $staffEntity = $this->createStaffUseCase->execute($dto, $permissions);

        if (!is_null($dto->workEmail)) {
            if (is_null($dto->password)) throw new InvalidArgumentException("Не указан пароль");

            $userDto = new UpdateUserData(
                active: true,
                email: $dto->workEmail,
                password: $dto->password,
                roleNames: ['staff'],
            );

            $this->registerStaffUserUseCase->execute(
                $staffEntity->id,
                $userDto,
                $permissions,
            );
        }

        return $staffEntity;
    }
}
