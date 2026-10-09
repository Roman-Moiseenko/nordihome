<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateDimensionsProductData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Parser\Domain\ValueObjects\Package;

readonly class SetDimensionsProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateDimensionsProductData $dto): ProductEntity
    {
        $applyToModification = $dto->modification ?? false;
        $targets = $this->resolveTargets($id, $applyToModification);

        foreach ($targets as $targetId => $variantsLine) {
            $product = $targetId === $id
                ? $this->productRepository->getById($id)
                : $this->productRepository->getById($targetId);

            if ($dto->dimensions !== null) {
                $product->dimensions = Dimensions::create(params: $dto->dimensions);
            }

            if ($dto->packages !== null) {
                $product->packages = array_map(
                    fn(array $item) => Package::fromArray($item),
                    $dto->packages,
                );
            }

            if ($dto->local !== null) {
                $product->local = $dto->local;
            }

            if ($dto->delivery !== null) {
                $product->delivery = $dto->delivery;
            }

            $product->complexity = trim($dto->complexity ?? '');

            $this->productRepository->save($product);
        }

        return $this->productRepository->getById($id);
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
