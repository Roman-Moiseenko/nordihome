<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions;

use App\Modules\Catalog\Application\DTOs\UpdateRoomData;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class UpdateRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    )
    {
    }

    public function execute(int $id, UpdateRoomData $dto, UserPermission $userPermission): RoomEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.update')) throw new \DomainException('Доступ запрещён');


        $room = $this->roomRepository->getById($id);

        // Обновляем поля, если переданы
        if ($dto->name !== null) {
            $room->name = $dto->name;
        }

        if ($dto->slug !== null) {
            $slug = new Slug($dto->slug);
            if ($this->roomRepository->existsSlug((string)$slug, $id)) {
                $slug = new Slug((string)$slug . '-' . uniqid());
            }
            $room->slug = $slug;
        }

        if ($dto->parentId !== null) {
            $room->parentId = $dto->parentId;
        }

        if ($dto->svgIcon !== null) {
            $room->svgIcon = $dto->svgIcon;
        }

        if ($dto->published !== null) {
            $dto->published ? $room->publish() : $room->unpublish();
        }

        // Обновляем Meta
        if ($dto->metaTitle !== null || $dto->metaDescription !== null) {
            $currentMeta = $room->meta ?? Meta::default();
            $room->meta = new Meta(
                title: $dto->metaTitle ?? $currentMeta->getTitle(),
                description: $dto->metaDescription ?? $currentMeta->getDescription(),
            );
        }

        return $this->roomRepository->save($room);
    }
}
