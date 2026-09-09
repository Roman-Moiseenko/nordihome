<?php

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Infrastructure\Models\LabelPost;

class LabelPostRepository implements LabelPostRepositoryInterface
{
    /** @inheritDoc */
    public function countPostsByLabelIds(array $labelIds): array
    {
        if (empty($labelIds)) {
            return [];
        }

        return LabelPost::select('label_id')
            ->selectRaw('COUNT(*) as count')
            ->whereIn('label_id', $labelIds)
            ->groupBy('label_id')
            ->pluck('count', 'label_id')
            ->toArray();
    }

    public function countPostsByLabelId(int $labelId): int
    {
        return LabelPost::where('label_id', $labelId)->count();
    }

    /** @inheritDoc */
    public function getLabelsByPostId(int $postId): array
    {
        return LabelPost::where('post_id', $postId)
            ->pluck('label_id')
            ->toArray();
    }

    /** @inheritDoc */
    public function syncLabels(int $postId, array $labelIds): void
    {
        LabelPost::where('post_id', $postId)->delete();

        foreach ($labelIds as $labelId) {
            $pivot = new LabelPost();
            $pivot->post_id = $postId;
            $pivot->label_id = $labelId;
            $pivot->save();
        }
    }
}
