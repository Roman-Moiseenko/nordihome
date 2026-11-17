<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Room\ToggleRoomUseCase;
use App\Modules\Catalog\Application\Actions\Wp\CreateRoomWpUseCase;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Application\Actions\GetPhotoByEntityUseCase;
use App\Modules\Shared\Application\Actions\UploadPhotoByUrlUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadByUrlData;
use App\Modules\Shared\Domain\Entities\UserPermission;

class LoadRoomWpService
{
    private UserPermission $userPermission;
    private int $count = 0;
    public function __construct(
        private readonly CreateRoomWpUseCase     $roomWpUseCase,
        private readonly UploadPhotoByUrlUseCase $uploadPhotoByUrlUseCase,
        private readonly ToggleRoomUseCase $toggleRoomUseCase,
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly GetPhotoByEntityUseCase $getPhotoByEntityUseCase,
    )
    {
    }

    /**
     * Загрузить комнаты из WP массива (children корневого каталога)
     *
     * @param array $rooms Массив комнат $categories[self::ROOM_ID]['children']
     * @return int Количество созданных комнат
     */
    public function load(array $rooms): int
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            ['storage.photo.upload', 'catalog.category.create', 'catalog.category.edit']
        );

        $this->loadChildren($rooms['children'] ?? [], null);

        return $this->count;
    }

    private function createRoom(array $roomData, ?int $parentId):? RoomEntity
    {
        $dto = CategoryRoomWpData::fromWpArray($roomData, $parentId);
        if (!is_null($room = $this->roomRepository->findByWpId($dto->wpId))) {
            $dto = new PhotoByEntityData($room->id, 'catalog.room', 'image');
            $photo = $this->getPhotoByEntityUseCase->execute($dto);
            if (is_null($photo) && $roomData['img'])
                $this->loadImage($room->id, $roomData['img']);

            return $room;
        }

            $room = $this->roomWpUseCase->execute($dto, $this->userPermission);
        if (!is_null($room)) {
            if (!$room->isPublished())
                $this->toggleRoomUseCase->execute($room->id, $this->userPermission);

            if ($roomData['img']) $this->loadImage($room->id, $roomData['img']);
            $this->count++;
        }
        return $room;
    }

    /**
     * Рекурсивная загрузка дочерних категорий
     */
    private function loadChildren(array $children, ?int $parentId): void
    {
        if (empty($children)) return;

        foreach ($children as $childData) {
            $room = $this->createRoom($childData, $parentId);
            if ($room !== null)$this->loadChildren($childData['children'] ?? [], $room->id);
        }
    }


    private function loadImage(int $categoryId, string $url): void
    {
        $dtoPhoto = new PhotoUploadByUrlData(
            $categoryId,
            'catalog.room',
            'image',
            $url,
            false,
        );
        $this->uploadPhotoByUrlUseCase->execute($dtoPhoto);
    }

}
