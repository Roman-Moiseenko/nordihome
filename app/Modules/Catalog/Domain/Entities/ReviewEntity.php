<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\ReviewStatus;

final class ReviewEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $productId {
        get => $this->productId;
        set => $this->productId = $value;
    }

    public int $clientId {
        get => $this->clientId;
        set => $this->clientId = $value;
    }

    public string $text = '' {
        get => $this->text;
        set => $this->text = $value;
    }

    public int $rating {
        get => $this->rating;
        set => $this->rating = $value;
    }

    public ReviewStatus $status {
        get => $this->status;
        set => $this->status = $value;
    }

    public ?\DateTimeImmutable $publishedAt = null {
        get => $this->publishedAt;
        set => $this->publishedAt = $value;
    }

    public ?\DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?\DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(
        int $productId,
        int $clientId,
        int $rating,
        string $text = '',
    ) {
        $this->productId = $productId;
        $this->clientId = $clientId;
        $this->rating = $rating;
        $this->text = $text;
        $this->status = ReviewStatus::draft();
    }

    public function publish(): void
    {
        $this->status = ReviewStatus::published();
        $this->publishedAt = new \DateTimeImmutable();
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }
}
