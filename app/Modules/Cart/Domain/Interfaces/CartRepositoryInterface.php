<?php

namespace App\Modules\Cart\Domain\Interfaces;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Storefront\Application\DTOs\ClientContext;

interface CartRepositoryInterface
{
    public function save(CartItemEntity $entity, ClientContext $client): CartItemEntity;
    public function clearCart(ClientContext $client): void;

    /**
     * @param ClientContext $client
     * @return CartItemEntity[]
     */
    public function getAll(ClientContext $client): array;

    public function getItemByProductId(int $productId, ClientContext $client):? CartItemEntity;

    public function getItemById(int $id, ClientContext $client): CartItemEntity;

    public function removeByProductId(int $productId, ClientContext $client): void;


    /**
     * Вспомогательные методы для корзины Куки
     * Используются при переносе корзины из куки в хранилище
     */

    /**
     * @param ClientContext $client
     * @return CartItemEntity[]
     */
    public function getItemsCookie(ClientContext $client): array;

    public function clearCookie(ClientContext $client): void;


}
