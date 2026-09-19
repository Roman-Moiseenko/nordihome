<?php
declare(strict_types=1);

namespace App\Modules\Cart\Infrastructure\Persistence;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\CartRepositoryInterface;
use App\Modules\Cart\Infrastructure\Models\CartCookie;
use App\Modules\Cart\Infrastructure\Models\CartStorage;
use App\Modules\Storefront\Application\DTOs\ClientContext;


class CartRepository implements CartRepositoryInterface
{
    public function __construct(

    ){
    }

    public function clearCart(ClientContext $client): void
    {
        if (!is_null($client->id)) {
            CartStorage::where('client_id', $client->id)->delete();
        } else {
            CartCookie::where('user_ui', $client->uuid)->delete();
        }
    }

    /**
     * @param ClientContext $client
     * @return CartItemEntity[]
     */
    public function getAll(ClientContext $client): array
    {
        if (!is_null($client->id)) {
            $models = CartStorage::where('client_id', $client->id)->getModels();
        } else {
            $models = CartCookie::where('user_ui', $client->uuid)->getModels();
        }
        return array_map(fn($model) => $this->hydrate($model), $models);
    }

    public function getItemByProductId(int $productId, ClientContext $client):? CartItemEntity
    {
        if (!is_null($client->id)) {
            $query = CartStorage::where('client_id', $client->id);
        } else {
            $query = CartCookie::where('user_ui', $client->uuid);
        }

        $model = $query->where('product_id', $productId)->first();
        if (is_null($model)) return null;
        return $this->hydrate($model);
    }

    public function getItemById(int $id, ClientContext $client): CartItemEntity
    {
        if (!is_null($client->id)) {
            $model = CartStorage::find($id);
        } else {
            $model = CartCookie::find($id);
        }
        return $this->hydrate($model);
    }

    public function removeByProductId(int $productId, ClientContext $client): void
    {
        if (!is_null($client->id)) {
            $query = CartStorage::where('client_id', $client->id);
        } else {
            $query = CartCookie::where('user_ui', $client->uuid);
        }

        $query->where('product_id', $productId)->delete();
    }

    public function save(CartItemEntity $entity, ClientContext $client): CartItemEntity
    {

        if (!is_null($client->id)) {
            $model = $entity->id ? CartStorage::find($entity->id) : new CartStorage();
            $model->client_id = $client->id;

        } else {
            $model = $entity->id ? CartCookie::find($entity->id) : new CartCookie();
            $model->user_ui = $client->uuid;
        }

        $model->product_id = $entity->productId;
        $model->quantity = $entity->quantity;
        $model->check = $entity->check;
        $model->is_parser = $entity->isParser;
        $model->save();
        return $this->hydrate($model);
    }

    private function hydrate($model): CartItemEntity
    {
        $entity = new CartItemEntity(
            productId: $model->product_id,
            quantity: $model->quantity,
            isParser: $model->is_parser,
        );
        $entity->id = $model->id;
        $entity->check = $model->check;
        return $entity;
    }

    /**
     * Вспомогательные методы для корзины Куки
     * Используются при переносе корзины из куки в хранилище
     */


    /**
     * @param ClientContext $client
     * @return CartItemEntity[]
     */
    public function getItemsCookie(ClientContext $client): array
    {
        $models = CartCookie::where('user_ui', $client->uuid)->getModels();
        return array_map(fn($model) => $this->hydrate($model), $models);
    }

    public function clearCookie(ClientContext $client): void
    {
        CartCookie::where('user_ui', $client->uuid)->delete();
    }

}
