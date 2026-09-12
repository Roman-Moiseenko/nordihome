<?php
declare(strict_types=1);

namespace App\Modules\Cart\Presentation\Http\Controllers\Web;

use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Application\Services\AddProductToCartService;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CartController extends ShopController
{

    public function __construct(
        private readonly AddProductToCartService $addProductToCartService)
    {

    }

    public function view(Request $request)
    {
        return view('cart.index');
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

        return \response()->json('Товар добавлен в корзину');
    }
    /*


        public function remove(Request $request, Product $product) //sub, set_count, clear
        {
            $this->removeCartItemUseCase->execute($product->id);
            //$this->cart->remove($product->id);
          //  $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json(null);
        }

        #[Deprecated]
        public function clear(Request $request) //sub, set_count, clear
        {
            //TODO Сделать сервис
            if ($request->has('product_ids')) {
                foreach ($request->get('product_ids') as $productId) {
                    $this->removeCartItemUseCase->execute($productId);
                }
            } else {
                $this->clearCartUseCase->execute();
                //$this->cart->clear();
            }
          //  $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json(null);
        }

        #[Deprecated]
        public function cart(Request $request)
        {
           // $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json(null);
        }
    */

}
