<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Room\ToggleRoomUseCase;
use App\Modules\Catalog\Application\Actions\Wp\CreateRoomWpUseCase;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Application\Actions\UploadPhotoByUrlUseCase;
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
        $room = $this->roomWpUseCase->execute($dto, $this->userPermission);
        if (!is_null($room)) {
            if (!$room->isPublished())
                $this->toggleRoomUseCase->execute($room->id, $this->userPermission);

            if ($roomData['img'] != false) {
                $dtoPhoto = new PhotoUploadByUrlData(
                    $room->id,
                    'catalog.room',
                    'image',
                    $roomData['img']
                );
                $this->uploadPhotoByUrlUseCase->execute($dtoPhoto, $this->userPermission);
            }
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




}
