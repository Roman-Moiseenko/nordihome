<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('finished', false)->where('preorder', false)->orderByDesc('created_at')->all();
        return view('admin.sales.order.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        return view('admin.sales.order.show', compact('order'));
    }
}
