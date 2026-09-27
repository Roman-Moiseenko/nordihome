<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cabinet\Application\DTOs\Wish\WishClientIndexData;
use App\Modules\Cabinet\Infrastructure\Models\Wish;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

class ListWishClientQuery
{

    public function execute(int $clientId): array
    {
        $client = Client::find($clientId);

        return array_map(function (Wish $wish) {
            return new WishClientIndexData(
                id: $wish->id,
                image: GetPhotoStatic::gallery('catalog.product', $wish->product_id, 'thumb'),
                name: $wish->product->name,
                url: route('shop.product.view', $wish->product->slug),
            );
        },  $client->wishes()->getModels());

    }
}
