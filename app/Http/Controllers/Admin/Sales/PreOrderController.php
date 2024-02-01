<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PreOrderController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }
    public function index(Request $request)
    {
        $query = Order::where('finished', false)->where('preorder', true)->orderByDesc('created_at');
        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $orders = $query->paginate($pagination);
            $orders->appends(['p' => $pagination]);
        } else {
            $orders = $query->paginate($this->pagination);
        }
        return view('admin.sales.preorder.index', compact('orders', 'pagination'));
    }

    public function show(Request $request, Order $order)
    {
        return view('admin.sales.preorder.show', compact('order'));
    }
}
