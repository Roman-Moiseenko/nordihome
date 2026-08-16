<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Cabinet\Application\DTOs\ReviewClientData;
use App\Modules\Catalog\Entity\Review;

class GetReviewsClientQuery
{

    public function execute(int $clientId): array
    {
        //MAINDO Возвращаем список отзывов с пагинацией

        $reviewsRaw = Review::orderByDesc('created_at')->where('client_id', $clientId)->paginate(15);

        $reviews = [];

        foreach ($reviewsRaw as $review) {
            $reviews[] = new ReviewClientData(
                id: $review->id,
            );

        }

        return $reviews;
    }
}
