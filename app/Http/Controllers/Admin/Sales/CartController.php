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

        $query = Product::orderBy('name')->Has('cartStorages')->OrHas('cartCookies');

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $products = $query->paginate($pagination);
            $products->appends(['p' => $pagination]);
        } else {
            $products = $query->paginate($this->pagination);
        }

        return view('admin.sales.cart.index', compact( 'products', 'pagination'));
    }
}
