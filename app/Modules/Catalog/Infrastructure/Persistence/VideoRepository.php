<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Base\Entity\Video;
use App\Modules\Catalog\Domain\Interfaces\VideoRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;

class VideoRepository implements VideoRepositoryInterface
{
    public function getByProductId(int $productId): array
    {
        return Product::findOrFail($productId)
            ->videos()
            ->get()
            ->map(fn(Video $video) => [
                'id' => $video->id,
                'url' => $video->url ?? '',
                'caption' => $video->caption ?? '',
                'description' => $video->description ?? '',
            ])
            ->values()
            ->toArray();
    }

    public function syncVideos(int $productId, array $videos): void
    {
        $product = Product::findOrFail($productId);
        $product->videos()->delete();

        foreach (array_values($videos) as $index => $item) {
            $product->videos()->save(Video::register(
                url: (string) ($item['url'] ?? ''),
                caption: (string) ($item['caption'] ?? ''),
                description: (string) ($item['description'] ?? ''),
                sort: $index,
            ));
        }
    }
}
