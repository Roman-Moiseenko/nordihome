<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;


class ReserveController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:order']);
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {

            $query = Product::orderBy('name')->whereHas('orderItems', function ($query) {
                $query->whereHas('reserves', function ($query) {
                    $query->where('quantity', '>', 0);
                });
            });//->Has('reserves');
            $products = $this->pagination($query, $request, $pagination);
            return view('admin.order.reserve.index', compact('products', 'pagination'));
        });
    }
}
