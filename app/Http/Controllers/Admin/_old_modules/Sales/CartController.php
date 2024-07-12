<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use function view;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:order']);
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Product::orderBy('name')->Has('cartStorages')->OrHas('cartCookies');
            $products = $this->pagination($query, $request, $pagination);
            return view('admin.sales.cart.index', compact('products', 'pagination'));
        });
    }
}
