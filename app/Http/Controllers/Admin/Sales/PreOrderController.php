<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Http\Request;

class PreOrderController extends Controller
{
    public function index(Request $request)
    {
        $preorders = Order::where('finished', false)->where('preorder', true)->orderByDesc('created_at')->all();
        return view('admin.sales.order.index', compact('preorders'));
    }
}
