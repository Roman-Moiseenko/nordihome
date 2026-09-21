<?php

namespace App\Modules\Output\Presentation\Http\Controllers\Web;

use App\Modules\Output\Application\DTOs\ECommerce\ECommerceEventData;
use App\Modules\Output\Application\Queries\ECommerce\GetECommerceQuery;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Http\Request;

class ECommerceController extends ShopController
{
    public function __construct(
        private readonly GetECommerceQuery $eCommerceQuery,
    ) {}

    public function e_commerce(Request $request): \Illuminate\Http\JsonResponse
    {
        $dto = ECommerceEventData::validateAndCreate($request->all());
        $ecommerce = $this->eCommerceQuery->execute($dto);

        return \response()->json([
            'ecommerce' => [
                'currencyCode' => $ecommerce->currencyCode,
                $ecommerce->type => $ecommerce->items,
            ],
        ]);
    }
}
