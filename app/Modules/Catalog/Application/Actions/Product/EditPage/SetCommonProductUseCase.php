<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCommonProductData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\RoomProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class SetCommonProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryProductRepositoryInterface $categoryProductRepository,
        private RoomProductRepositoryInterface $roomProductRepository,
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateCommonProductData $dto): ProductEntity
    {
        $name = trim($dto->name);
        $namePrint = trim($dto->namePrint);
        $slugValue = trim($dto->slug ?? '');
        $applyToModification = $dto->modification ?? false;

        $current = $this->productRepository->getById($id);

        // Артикул — индивидуальное поле: обновляется только у редактируемого товара.
        $current->code = new Code(trim($dto->code));

        // Список товаров для обновления: либо только текущий,
        // либо все товары модификации (productId => строка вариантов).
        $targets = $this->resolveTargets($id, $applyToModification);
        $productsCount = count($targets);

        foreach ($targets as $targetId => $variantsLine) {
            $product = $targetId === $id
                ? $current
                : $this->productRepository->getById($targetId);

            if ($product->name !== $name) {
                $product->name = $name . $variantsLine;
            }

            if ($product->namePrint !== $namePrint) {
                $product->namePrint = $namePrint . $variantsLine;
            }

            if ($applyToModification) {
                // Для модификаций перегенерируем slug только если поле пустое.
                if ($slugValue === '') {
                    $product->slug = new Slug(Str::slug($product->name));
                }
            } else {
                $product->slug = new Slug($slugValue !== '' ? $slugValue : Str::slug($product->name));
            }

            $product->comment = trim($dto->comment ?? '');
            $product->mainCategoryId = $dto->categoryId;
            $product->brandId = $dto->brandId;
            $product->countryId = $dto->countryId;
            $product->measuringId = $dto->measuringId;
            $product->markingTypeId = $dto->markingTypeId;

            if ($dto->fractional !== null) {
                $product->fractional = $dto->fractional;
            }

            $this->productRepository->save($product);

            // Доп.категории и комнаты — тоже для всех товаров из списка.
            if ($dto->categories !== null) {
                $this->categoryProductRepository->syncCategories($targetId, $this->toInts($dto->categories));
            }

            if ($dto->rooms !== null) {
                $this->roomProductRepository->syncRooms($targetId, $this->toInts($dto->rooms));
            }
        }

        return $this->productRepository->getById($id);
    }

    /**
     * @param array<int|string, mixed> $values
     * @return int[]
     */
    private function toInts(array $values): array
    {
        return array_values(array_unique(array_map('intval', $values)));
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
