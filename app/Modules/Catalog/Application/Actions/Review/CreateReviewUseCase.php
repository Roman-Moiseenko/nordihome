<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Review;

use App\Modules\Catalog\Application\DTOs\Review\ReviewCreateData;
use App\Modules\Catalog\Domain\Entities\ReviewEntity;
use App\Modules\Catalog\Domain\Interfaces\ReviewRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\ReviewStatus;
use App\Modules\Shop\Application\DTOs\ClientContext;

readonly class CreateReviewUseCase
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepository,
    ) {}

    public function execute(ReviewCreateData $dto, ClientContext $clientContext): ReviewEntity
    {
        if ($clientContext->id === null) {
            throw new \DomainException('Отзыв можно оставлять только авторизованным клиентам');
        }

        $review = new ReviewEntity(
            productId: $dto->productId,
            clientId: $clientContext->id,
            rating: $dto->rating,
            text: $dto->text ?? '',
        );

        $review->status = ReviewStatus::draft();

        return $this->reviewRepository->save($review);
    }
}
