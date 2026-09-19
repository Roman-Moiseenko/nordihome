<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Cart\Application\DTOs\CartInfoData;
use App\Modules\Cart\Application\DTOs\CartItemData;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Order\Application\Queries\GetSellPriceQuery;
use App\Modules\Parser\Application\Actions\Product\GetParserPriceByProductUseCase;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Application\Actions\GetPhotoThumbUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoThumbData;
use App\Modules\Storefront\Application\DTOs\ClientContext;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class GetCartUseCase
{

    const DELIVERY_PERIOD = [
        ['min' => 0, 'max' => 5, 'value' => 180, 'slug' => 'parser_delivery_0'],
        ['min' => 5, 'max' => 10, 'value' => 160, 'slug' => 'parser_delivery_1'],
        ['min' => 10, 'max' => 15, 'value' => 140, 'slug' => 'parser_delivery_2'],
        ['min' => 15, 'max' => 30, 'value' => 105, 'slug' => 'parser_delivery_3'],
        ['min' => 30, 'max' => 40, 'value' => 85, 'slug' => 'parser_delivery_4'],
        ['min' => 40, 'max' => 50, 'value' => 75, 'slug' => 'parser_delivery_5'],
        ['min' => 50, 'max' => 200, 'value' => 70, 'slug' => 'parser_delivery_6'],
        ['min' => 200, 'max' => 300, 'value' => 68, 'slug' => 'parser_delivery_7'],
        ['min' => 300, 'max' => 400, 'value' => 66, 'slug' => 'parser_delivery_8'],
        ['min' => 400, 'max' => 600, 'value' => 63, 'slug' => 'parser_delivery_9'],
        ['min' => 600, 'max' => 9999999, 'value' => 60, 'slug' => 'parser_delivery_10'],
    ];

    public function __construct(
        private CartRepositoryInterface          $cartRepository,
        private Settings                         $settings,
        private GetParserPriceByProductUseCase   $getParserPriceByProductUseCase,
        private GetSellPriceQuery                $productSellPriceUseCase,
        private ProductRepositoryInterface       $productRepository,
        private ParserProductRepositoryInterface $parserProductRepository,
        private GetPhotoThumbUseCase             $getPhotoThumbUseCase,
    )
    {

    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(ClientContext $clientContext): CartInfoData
    {
        $cartItems = $this->cartRepository->getAll($clientContext);
        $items = [];
        $amount = 0;
        $discount = 0;
        $quantity = 0;

        $amountCheck = 0;
        $discountCheck = 0;
        $quantityCheck = 0;
        $weight = 0;
        $fragile = 0;

        $priceType = new PriceType($clientContext->priceType);
        foreach ($cartItems as $item) {
            $productEntity = $this->productRepository->getById($item->productId); //Получить Товар,
            $productPrice = null; //
            if ($item->isParser) {
                $url = route('shop.ikea.product', $productEntity->code->getCodeSearch());
                $price = $this->getParserPriceByProductUseCase->execute($item->productId); // $item->base_cost * (1 + (int)$item->product->parser->sanctioned * $sanctioned / 100) * $ratio;
            } else {
                $url = route('shop.product.view', $productEntity->slug);
                $productPrice = $this->productSellPriceUseCase->execute($item->productId, $priceType);
                //Получить текущую цену для текущего клиента
                $price = $productPrice->basePrice;
            }

            $itemData = new CartItemData(
                id: $item->id,
                cost: $price * $item->quantity,
                price: $price,
                quantity: $item->quantity,
                check: $item->check,
                isParser: $item->isParser,
                //ProductInfo
                productId: $item->productId,
                name: $productEntity->name,
                image: $this->getUrlImage($item->productId),
                url: $url,
                ///DiscountInfo
                discountId: is_null($productPrice) ? null : $productPrice->discountId,
                discountPrice: is_null($productPrice?->discountId) ? null : $productPrice->sellPrice * $item->quantity,
                discountName: is_null($productPrice) ? null : $productPrice->discountName,
            );
            $amount += $itemData->cost;
            if ($itemData->discountPrice > 0) $discount += ($itemData->cost - $itemData->discountPrice);
            $quantity += $itemData->quantity;
            if ($itemData->check) {
                $amountCheck += $itemData->cost;
                if ($itemData->discountPrice > 0) $discountCheck += ($itemData->cost - $itemData->discountPrice);
                $quantityCheck += $itemData->quantity;

                //Для парсера доп.расчет
                if ($itemData->isParser) {
                    $parserEntity = $this->parserProductRepository->getByProductId($item->productId);

                    $weightParser = $parserEntity->weight();
                    $weight += $weightParser * $itemData->quantity;
                    if ($parserEntity->fragile) $fragile += $weightParser * $itemData->quantity;
                }
            }

            $items[] = $itemData;
        };
        $deliveryParser = $this->getCostDelivery($weight, $fragile);

        return new CartInfoData(
            items: $items,
            amount: $amount,
            discount: $discount,
            quantity: $quantity,
            amountCheck: $amountCheck,
            discountCheck: $discountCheck,
            quantityCheck: $quantityCheck,
            delivery: 0,
            deliveryParser: $deliveryParser,
        );
    }

    private function getUrlImage(int $productId): string
    {
        $dto = new PhotoThumbData(
            imageableId: $productId,
            modelType: 'catalog.product',
            type: 'gallery',
            thumb: 'mini',
        );
        return $this->getPhotoThumbUseCase->execute($dto);
    }

    private function getCostDelivery(float $weight, float $fragile): float
    {
        if ($weight == 0) return 0;
        $parser = $this->settings->getParser();

        $cost = 0;
        foreach (self::DELIVERY_PERIOD as $item) {
            if ($item['min'] < $weight & $weight <= $item['max']) {
                $slug = $item['slug'];
                $cost = $parser->$slug;
                break;
            }
        }

        $amount = $weight * $cost + $fragile * $parser->cost_weight_fragile;
        return max($parser->parser_delivery, $amount);
    }
}
