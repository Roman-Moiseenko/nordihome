<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class WishController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Product::orderBy('name')->Has('wishes');
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $products = $query->paginate($pagination);
                $products->appends(['p' => $pagination]);
            } else {
                $products = $query->paginate($this->pagination);
            }
            return view('admin.sales.wish.index', compact('products', 'pagination'));
        });
    }
}
