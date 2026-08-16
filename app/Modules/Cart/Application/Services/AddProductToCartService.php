<?php

namespace App\Modules\Cart\Application\Services;

use App\Modules\Cart\Application\Actions\AddToCartUseCase;
use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Parser\Application\Actions\Product\ViewParserProductUseCase;
use App\Modules\Parser\Application\Services\CreateProductFromParserService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Contracts\Container\BindingResolutionException;

class AddProductToCartService
{

    private UserPermission $userPermission;
    public function __construct(
        private readonly CreateProductFromParserService $createProductFromParserService,
        private readonly ViewParserProductUseCase       $viewParserProductUseCase,

        private readonly AddToCartUseCase $addToCartUseCase,
    )
    {

    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(AddProductToCartData $dto): void
    {
        $userPermission = new UserPermission(null, ['admin'], [
            'parser.product.view', 'catalog.product.create', 'catalog.product.edit',
            ]);
        //1. Товар Из парсера или нет
        if ($dto->isParser) {
            //если товар из парсера, то всегда id == parser_products.id
            //необходимо подменить на products.id

            $parserProduct = $this->viewParserProductUseCase->execute($dto->id, $userPermission);
            if ($parserProduct->productId == null)
            {
                //Создаем Товар из ParserProduct
                $productEntity = $this->createProductFromParserService->execute($dto->id, $userPermission);
                $dto->id = $productEntity->id;

            } else {
                $dto->id = $parserProduct->productId;
            }


        }
        //2. Есть ли сам товар в базе

        //TODO Проверка остатков через UseCase
        /*
            if (!$product->pre_order && !$this->pre_order && $product->getQuantitySell() < $quantity) {
            throw new \DomainException('Превышение остатка');
        }
         */

        //TODO Добавляем аналитику, сохранение данных, например, id товара и id, uuid клиента

        //TODO AddToCartUseCase
        //4. Товар в корзину
        $this->addToCartUseCase->execute($dto);
        //$this->cart->add($product, $dto->quantity, $dto->isParser);
        //Возврат true / false
    }
}
