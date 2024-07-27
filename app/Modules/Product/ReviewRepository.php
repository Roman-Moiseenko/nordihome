<?php
declare(strict_types=1);

namespace App\Modules\Product;

use App\Modules\Product\Entity\Review;

class ReviewRepository
{

    public function getIndex(string $filter)
    {
        $query = Review::orderByDesc('created_at');
        if ($filter == 'moderated') $query->where('status', Review::STATUS_MODERATED);
        if ($filter == 'draft') $query->where('status', Review::STATUS_DRAFT);
        if ($filter == 'published') $query->where('status', Review::STATUS_PUBLISHED);
        if ($filter == 'blocked') $query->where('status', Review::STATUS_BLOCKED);
        return $query;
    }

    public function countModerated(): int
    {
        return  Review::where('status', Review::STATUS_MODERATED)->count();
    }
}
