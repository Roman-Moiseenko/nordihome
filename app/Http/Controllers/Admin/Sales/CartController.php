<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {

        $products = Product::orderBy('name');
        //TODO

       /* $cart_cookie = CartCookie::orderByDesc('created_at')->get();
        $cart_storage = CartStorage::orderByDesc('created_at')->get();*/
/*
        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $deliveries = $query->paginate($pagination);
            $deliveries->appends(['p' => $pagination]);
        } else {
            $deliveries = $query->paginate($this->pagination);
        }
*/
        return view('admin.sales.cart.index', compact( 'products'));
    }
}
