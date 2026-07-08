<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Shop\Application\DTOs\RoomTreeClientData;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomTreeQueryRepository;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class GetRoomTreeQuery
{
    private const CACHE_KEY = 'client_room_tree';

    public function __construct(
        private RoomTreeQueryRepository $repository
    ) {}

    /** @return RoomTreeClientData[]
     * @throws LockTimeoutException
     */
    public function execute(): array
    {
        // Попытка прочитать из кеша
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }

        // Блокировка, чтобы избежать множественной сборки при холодном старте
        $lock = Cache::lock('build_' . self::CACHE_KEY, 10);
        try {
            if ($lock->block(5)) {
                // Повторная проверка после блокировки
                if ($cached = Cache::get(self::CACHE_KEY)) {
                    return $cached;
                }
                $tree = $this->repository->getFullTree();
                Cache::put(self::CACHE_KEY, $tree, now()->addDay());
                return $tree;
            }
        } finally {
            optional($lock)->release();
        }

        // Fallback без кеша
        return $this->repository->getFullTree();
    }
}
