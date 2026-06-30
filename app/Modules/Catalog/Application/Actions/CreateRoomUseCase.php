<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\RoomCreateData;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    public function execute(RoomCreateData $dto, UserPermission $userPermission): RoomEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.create')) throw new \DomainException('Доступ запрещён');


        $slug = new Slug($dto->slug ?: $dto->name);

        // Если slug занят, добавляем суффикс
        if ($this->roomRepository->existsSlug((string)$slug)) {
            $slug = new Slug((string)$slug . '-' . uniqid());
        }

        $room = new RoomEntity(
            name: $dto->name,
            slug: $slug,
            parentId: $dto->parentId ?: null,
        );

        return $this->roomRepository->save($room);
    }
}
