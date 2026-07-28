<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Application\DTOs\Cart\AddProductToCartData;
use App\Modules\Shop\Application\Services\AddProductToCartService;
use App\Modules\Shop\Cart\Cart;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CartController extends ShopController
{
    private Cart $cart;

    public function __construct(Cart $cart,
    private readonly AddProductToCartService $addProductToCartService)
    {
        parent::__construct();
        $this->cart = $cart;
    }

    public function view(Request $request)
    {
            $cart = $this->cart->getCartToFront($request['tz']);
            return view($this->route('cart'), compact('cart'));
    }

    //AJAX

    /**
     * @throws BindingResolutionException
     */
    public function add(Request $request): JsonResponse
    {
        $dto = AddProductToCartData::validateAndCreate($request->all());
        $this->addProductToCartService->execute($dto);

        return \response()->json('Товар добавлен в корзину');
    }


    public function remove(Request $request, Product $product) //sub, set_count, clear
    {
            $this->cart->remove($product->id);
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
    }

    public function clear(Request $request) //sub, set_count, clear
    {
            if ($request->has('product_ids')) {
                $this->cart->removeByIds($request->get('product_ids'));
            } else {
                $this->cart->clear();
            }
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
    }

    public function cart(Request $request)
    {
            $cart = $this->cart->getCartToFront($request['tz']);
            return \response()->json($cart);
    }


}
