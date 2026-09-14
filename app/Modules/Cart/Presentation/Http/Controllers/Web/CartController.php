<?php
declare(strict_types=1);

namespace App\Modules\Cart\Presentation\Http\Controllers\Web;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Presentation\Support\RecordsAnalyticsAction;
use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Application\Queries\PageCartQuery;
use App\Modules\Cart\Application\Services\AddProductToCartService;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CartController extends ShopController
{
    use RecordsAnalyticsAction;

    public function __construct(
        private readonly AddProductToCartService $addProductToCartService,
        private readonly PageCartQuery           $pageCartQuery,
    )
    {

    }

    public function view(Request $request)
    {
        $data = $this->pageCartQuery->execute($this->getClient($request));
        return view('cart.index', ['pageData' => $data]);
    }

    //AJAX

    /**
     * @throws BindingResolutionException
     */
    public function add(Request $request): JsonResponse
    {
        $client = $this->getClient($request);
        $dto = AddProductToCartData::validateAndCreate($request->all());

        $this->addProductToCartService->execute($dto, $client);


        $this->recordAnalyticsAction(
            ActionType::CART_ADD,
            EntityType::PRODUCT,
            $dto->id,
            ['quantity' => $dto->quantity, 'isParser' => $dto->isParser],
        );

        return \response()->json('Товар добавлен в корзину');
    }

}
