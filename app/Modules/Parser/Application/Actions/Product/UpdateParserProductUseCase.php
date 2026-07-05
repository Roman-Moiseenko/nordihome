<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\DTOs\Product\ParserProductUpdateData;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\ValueObjects\Composite;
use App\Modules\Parser\Domain\ValueObjects\Package;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class UpdateParserProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    )
    {
    }

    public function execute(ParserProductUpdateData $dto): ParserProductEntity
    {
        // Получаем текущую сущность
        $product = $this->productRepository->getById($dto->id);

        // Обновляем только те поля, которые переданы в DTO (не null)
        if ($dto->name !== null) {
            $product->name = $dto->name;
        }
        if ($dto->code !== null) {
            $product->code = $dto->code;
        }
        if ($dto->slug !== null) {
            $product->slug = new Slug($dto->slug);
        }
        if ($dto->url !== null) {
            $product->url = $dto->url;
        }
        if ($dto->priceSell !== null) {
            $product->priceSell = $dto->priceSell;
        }
        if ($dto->priceBase !== null) {
            $product->priceBase = $dto->priceBase;
        }
        if ($dto->short !== null) {
            $product->short = $dto->short;
        }
        if ($dto->description !== null) {
            $product->description = $dto->description;
        }
        if ($dto->fragile !== null) {
            $product->fragile = $dto->fragile;
        }
        if ($dto->sanctioned !== null) {
            $product->sanctioned = $dto->sanctioned;
        }
        if ($dto->availability !== null) {
            $product->availability = $dto->availability;
        }
        if ($dto->composite !== null) {
            $product->composite = array_map(
                fn(array $item) => new Composite(
                    productId: (int) ($item['product_id'] ?? 0),
                    quantity: (int) ($item['quantity'] ?? 1),
                ),
                $dto->composite
            );
        }
        if ($dto->packages !== null) {
            $product->packages = array_map(
                fn(Package|array $item) => $item instanceof Package ? $item : Package::fromArray($item),
                $dto->packages
            );
        }
        if ($dto->colors !== null) {
            $product->colors = $dto->colors;
        }

        return $this->productRepository->save($product);
    }
}
