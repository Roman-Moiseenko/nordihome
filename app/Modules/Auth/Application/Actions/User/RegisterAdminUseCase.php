<?php

namespace App\Modules\Auth\Application\Actions\User;

use App\Modules\Auth\Application\DTOs\AdminData;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Application\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Auth\Domain\Services\PasswordHasherInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\HashedPassword;
use App\Modules\Auth\Domain\ValueObjects\ProfileType;
use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Auth\Infrastructure\Models\Staff;

/**
 * Для консольной команды
 */
readonly class RegisterAdminUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository,
                                private StaffRepositoryInterface $staffRepository,
                                private PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(AdminData $dto): UserEntity
    {
        $email = new Email($dto->email);

        if ($this->userRepository->emailExists($email)) {
            throw new UserAlreadyExistsException("Пользователь с email {$dto->email} уже существует");
        }
        $user = new UserEntity(
            $email,
            HashedPassword::fromPlainText($dto->password, $this->passwordHasher),
        );

        $user->roles = [RoleName::ADMIN];

        $fullName = new FullName("Admin Admin");

        $staff = new StaffEntity(
            $fullName,
            new StaffPositions([StaffPosition::ADMINISTRATOR]),
        );

        $staff = $this->staffRepository->save($staff);
        $user->setProfile(ProfileType::STAFF, $staff->id);

        return $this->userRepository->save($user);
    }
}
