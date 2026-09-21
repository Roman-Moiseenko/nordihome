<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Output\Application\DTOs\Feed\FeedUpdateData;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

/**
 * Единый UseCase обновления фида.
 *
 * Отправляется только изменяемый параметр:
 *  - скалярные поля (name, setPreprice, setTitle, setDescription);
 *  - мутация списка (field + action add|remove|clear + in + ids).
 * Для товаров ids может приходить массивом, но это всегда только
 * добавление к текущему списку.
 */
readonly class UpdateFeedUseCase
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
    ) {}

    public function execute(int $id, FeedUpdateData $dto, UserPermission $userPermission): FeedEntity
    {
        if (!$userPermission->can('output.feed.edit')) {
            throw new AccessDeniedException();
        }

        $feed = $this->feedRepository->getById($id);

        $this->applyScalar($feed, $dto);

        if ($dto->field !== null && $dto->action !== null) {
            $this->applyList($feed, $dto);
        }

        return $this->feedRepository->save($feed);
    }

    private function applyScalar(FeedEntity $feed, FeedUpdateData $dto): void
    {
        if ($dto->name !== null) {
            $feed->name = $dto->name;
        }

        if ($dto->setPreprice !== null) {
            $feed->setPreprice = $dto->setPreprice;
        }

        if ($dto->active !== null) {
            $feed->active = $dto->active;
        }

        if ($dto->setTitle !== null) {
            $feed->setTitle = $dto->setTitle === '' ? null : $dto->setTitle;
        }

        if ($dto->setDescription !== null) {
            $feed->setDescription = $dto->setDescription === '' ? null : $dto->setDescription;
        }

        if ($dto->priceChanged === true) {
            $feed->priceMin = $dto->priceMin;
            $feed->priceMax = $dto->priceMax;
        }
    }

    private function applyList(FeedEntity $feed, FeedUpdateData $dto): void
    {
        $ids = array_values(array_unique(array_map('intval', $dto->ids ?? [])));
        $in = (bool) $dto->in;

        if ($dto->field === 'tags' && $dto->action === 'add') {
            // Метка не может одновременно находиться в In и Out
            $opposite = array_values(array_diff(
                $this->getList($feed, 'tags', !$in),
                $ids,
            ));
            $this->setList($feed, 'tags', !$in, $opposite);
        }

        $current = $this->getList($feed, $dto->field, $in);

        $this->setList($feed, $dto->field, $in, match ($dto->action) {
            'add' => array_values(array_unique(array_merge($current, $ids))),
            'remove' => array_values(array_diff($current, $ids)),
            'clear' => [],
            default => $current,
        });
    }

    /**
     * @return int[]
     */
    private function getList(FeedEntity $feed, string $field, bool $in): array
    {
        return match ($field) {
            'products' => $in ? $feed->productsIn : $feed->productsOut,
            'tags' => $in ? $feed->tagsIn : $feed->tagsOut,
            'categories' => $in ? $feed->categoriesIn : $feed->categoriesOut,
            'rooms' => $in ? $feed->roomsIn : $feed->roomsOut,
            'promotions' => $in ? $feed->promotionsIn : $feed->promotionsOut,
            'groups' => $in ? $feed->groupsIn : $feed->groupsOut,
            default => throw new \InvalidArgumentException("Unknown field: {$field}"),
        };
    }

    /**
     * @param int[] $value
     */
    private function setList(FeedEntity $feed, string $field, bool $in, array $value): void
    {
        switch ($field) {
            case 'products':
                if ($in) { $feed->productsIn = $value; } else { $feed->productsOut = $value; }
                break;
            case 'tags':
                if ($in) { $feed->tagsIn = $value; } else { $feed->tagsOut = $value; }
                break;
            case 'categories':
                if ($in) { $feed->categoriesIn = $value; } else { $feed->categoriesOut = $value; }
                break;
            case 'rooms':
                if ($in) { $feed->roomsIn = $value; } else { $feed->roomsOut = $value; }
                break;
            case 'promotions':
                if ($in) { $feed->promotionsIn = $value; } else { $feed->promotionsOut = $value; }
                break;
            case 'groups':
                if ($in) { $feed->groupsIn = $value; } else { $feed->groupsOut = $value; }
                break;
            default:
                throw new \InvalidArgumentException("Unknown field: {$field}");
        }
    }
}
