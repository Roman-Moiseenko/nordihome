<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;

class ExecutedController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('finished', true)->orderByDesc('created_at')->all();
        return view('admin.sales.order.index', compact('orders'));
    }
}
