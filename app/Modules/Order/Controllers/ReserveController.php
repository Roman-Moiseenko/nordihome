<?php
declare(strict_types=1);

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ReserveController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        return redirect()->back()->with('warning', 'В разработке');

        $query = Product::orderBy('name')->whereHas('orderItems', function ($query) {
            $query->whereHas('reserves', function ($query) {
                $query->where('quantity', '>', 0);
            });
        });
        $products = $this->pagination($query, $request, $pagination);
        //TODO Добавить список заказов, у которых товар в резерве, с переходом к Заказу
        return Inertia::render('Order/Reserve/Index', [
            'products' => $products,

        ]);

        //return view('admin.order.reserve.index', compact('products', 'pagination'));
    }
}
