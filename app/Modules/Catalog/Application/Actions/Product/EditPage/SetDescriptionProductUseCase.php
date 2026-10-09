<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDescriptionProductData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class SetDescriptionProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private TagProductRepositoryInterface $tagProductRepository,
        private TagRepositoryInterface $tagRepository,
        private SeriesRepositoryInterface $seriesRepository,
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateDescriptionProductData $dto): ProductEntity
    {
        $applyToModification = $dto->modification ?? false;

        $targets = $this->resolveTargets($id, $applyToModification);
        $seriesId = $this->resolveSeriesId($dto->seriesId);
        $tagIds = $this->resolveTagIds($dto->tags ?? []);

        foreach ($targets as $targetId => $variantsLine) {
            $product = $targetId === $id
                ? $this->productRepository->getById($id)
                : $this->productRepository->getById($targetId);

            $product->description = trim($dto->description ?? '');
            $product->short = trim($dto->short ?? '');
            $product->care = trim($dto->care ?? '');
            $product->model = trim($dto->model ?? '');
            $product->seriesId = $seriesId;

            $this->productRepository->save($product);

            if ($dto->tags !== null) {
                $this->tagProductRepository->syncTags($targetId, $tagIds);
            }
        }

        return $this->productRepository->getById($id);
    }

    /**
     * @param array<int, int|string> $tags
     * @return int[]
     */
    private function resolveTagIds(array $tags): array
    {
        $ids = [];

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                $ids[] = (int) $tag;
                continue;
            }

            $name = trim((string) $tag);
            if ($name === '') {
                continue;
            }

            $entity = $this->tagRepository->findByName($name);
            if ($entity === null) {
                $entity = $this->tagRepository->save(
                    new TagEntity($name, new Slug(Str::slug($name)))
                );
            }

            $ids[] = $entity->id;
        }

        return array_values(array_unique($ids));
    }

    private function resolveSeriesId(int|string|null $seriesId): ?int
    {
        if ($seriesId === null) {
            return null;
        }

        if (is_numeric($seriesId)) {
            return (int) $seriesId;
        }

        $name = trim((string) $seriesId);
        if ($name === '') {
            return null;
        }

        $series = $this->seriesRepository->getByName($name);
        if ($series === null) {
            $series = $this->seriesRepository->save(new SeriesEntity($name));
        }

        return $series->id;
    }

    /**
     * @return array<int, string> productId => строка вариантов (" 1 2")
     */
    private function resolveTargets(int $id, bool $applyToModification): array
    {
        if (!$applyToModification) {
            return [$id => ''];
        }

        $modification = $this->getModificationByProductQuery->execute($id);

        if ($modification === null) {
            return [$id => ''];
        }

        // productId => названия выбранных вариантов (в порядке осей модификации).
        $items = [];
        foreach ($modification->products as $product) {
            $items[$product->productId] = $product->values;
        }

        if (!isset($items[$id])) {
            $items[$id] = [];
        }

        $count = count($items);

        $targets = [];
        foreach ($items as $productId => $values) {
            $targets[$productId] = ($count > 1 && $values !== [])
                ? ' ' . implode(' ', $values)
                : '';
        }

        return $targets;
    }
}
