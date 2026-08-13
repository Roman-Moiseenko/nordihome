<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Illuminate\Support\Facades\DB;

class RoomSearchQueryRepository
{
    private const string MODEL_TYPE = 'catalog.room';

    /**
     * Поля таблицы rooms, по которым производится поиск.
     */
    private const array ROOM_SEARCH_FIELDS = [
        'name',
    ];

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    /**
     * Возвращает массив ID комнат, соответствующих поисковому запросу.
     *
     * @return int[]
     */
    public function getRoomIdsBySearch(string $search): array
    {
        $words = $this->splitSearchIntoWords($search);

        if (empty($words)) {
            return [];
        }

        $result = null;

        foreach ($words as $word) {
            $ids = $this->findRoomIdsByWord($word);

            if ($result === null) {
                $result = $ids;
            } else {
                $result = array_intersect($result, $ids);
            }

            if (empty($result)) {
                return [];
            }
        }

        return array_values($result);
    }

    /**
     * Загрузка упрощённых карточек комнат для полнотекстового поиска.
     *
     * @param int[] $ids
     * @return array<int, array{id: int, name: string, url: string, code: null, image: string|null, price: null}>
     */
    public function loadRoomSearchItems(array $ids, ClientContext $clientContext): array
    {
        if (empty($ids)) {
            return [];
        }

        $orderedIds = implode(',', array_map('intval', $ids));

        $rows = DB::table('rooms')
            ->whereIn('rooms.id', $ids)
            ->orderByRaw("FIELD(rooms.id, $orderedIds)")
            ->leftJoin('photos', function ($join) {
                $join->on('rooms.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'rooms.id',
                'rooms.name',
                'rooms.slug',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.thumb as photo_thumb',
            )
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'url' => route('shop.room.view', $row->slug),
                'code' => null,
                'image' => $this->buildImageUrl($row),
                'price' => null,
            ];
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private function splitSearchIntoWords(string $search): array
    {
        $words = explode(' ', trim($search));

        return array_values(
            array_filter($words, static fn(string $word): bool => $word !== '')
        );
    }

    /**
     * @return int[]
     */
    private function findRoomIdsByWord(string $word): array
    {
        $pattern = '%' . mb_strtolower($word) . '%';

        return DB::table('rooms')
            ->where(function ($query) use ($pattern) {
                foreach (self::ROOM_SEARCH_FIELDS as $field) {
                    $query->orWhereRaw('LOWER(COALESCE(' . $field . ', \'\')) LIKE ?', [$pattern]);
                }
            })
            ->pluck('id')
            ->map(static fn($id) => (int) $id)
            ->toArray();
    }

    private function buildImageUrl(\stdClass $item): ?string
    {
        if (empty($item->photo_file) || empty($item->photo_id)) {
            return null;
        }

        return $this->photoService->getThumbUrl(
            photoId: (int) $item->photo_id,
            modelType: self::MODEL_TYPE,
            imageableId: (int) $item->id,
            fileName: $item->photo_file,
            thumb: 'catalog',
            isThumbEnabled: (bool) $item->photo_thumb,
        );
    }
}
