<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Review\FilterReviewIndexData;
use App\Modules\Catalog\Domain\Entities\ReviewEntity;
use App\Modules\Catalog\Domain\Interfaces\ReviewRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\ReviewStatus;
use App\Modules\Catalog\Infrastructure\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function getFilteredPaginated(FilterReviewIndexData $filter): LengthAwarePaginator
    {
        $query = Review::orderByDesc('created_at');

        $filter->count = 0;

        if (!is_null($filter->status) && $filter->status !== '') {
            $query->where('status', $filter->status);
            $filter->count++;
        }

        if (!is_null($filter->dateFrom) && $filter->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $filter->dateFrom);
            $filter->count++;
        }

        if (!is_null($filter->dateTo) && $filter->dateTo !== '') {
            $query->whereDate('created_at', '<=', $filter->dateTo);
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->withQueryString()
            ->through(fn(Review $model) => $this->hydrate($model));
    }

    public function getByProductIdPaginated(int $productId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Review::where('product_id', $productId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Review $model) => $this->hydrate($model));
    }

    public function getByClientIdPaginated(int $clientId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Review::where('client_id', $clientId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Review $model) => $this->hydrate($model));
    }

    public function getById(int $id): ReviewEntity
    {
        $model = Review::find($id);

        if (!$model) {
            throw new ModelNotFoundException("Review with id {$id} not found");
        }

        return $this->hydrate($model);
    }

    public function save(ReviewEntity $review): ReviewEntity
    {
        $model = $review->id
            ? Review::findOrFail($review->id)
            : new Review();

        $model->product_id = $review->productId;
        $model->client_id = $review->clientId;
        $model->text = $review->text;
        $model->rating = $review->rating;
        $model->status = (string) $review->status;

        if ($review->publishedAt !== null) {
            $model->published_at = $review->publishedAt;
        }

        $model->save();

        return $this->hydrate($model->fresh());
    }

    private function hydrate(Review $model): ReviewEntity
    {
        $entity = new ReviewEntity(
            productId: $model->product_id,
            clientId: $model->client_id,
            rating: (int) $model->rating,
            text: $model->text ?? '',
        );

        $entity->id = $model->id;
        $entity->status = ReviewStatus::fromString($model->status ?: ReviewStatus::DRAFT);
        $entity->publishedAt = $model->published_at;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }
}
