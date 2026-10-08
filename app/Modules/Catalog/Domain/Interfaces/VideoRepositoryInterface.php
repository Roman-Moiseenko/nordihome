<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

interface VideoRepositoryInterface
{
    /**
     * Видео товара.
     *
     * @return array<int, array{id: int, url: string, caption: string, description: string}>
     */
    public function getByProductId(int $productId): array;

    /**
     * Полная замена списка видео товара.
     *
     * @param array<int, array{url?: string, caption?: string, description?: string}> $videos
     */
    public function syncVideos(int $productId, array $videos): void;
}
