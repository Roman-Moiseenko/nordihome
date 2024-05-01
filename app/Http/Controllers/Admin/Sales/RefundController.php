<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\OrderRefund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:order']);
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            //Фильтры ??
            $query = OrderRefund::orderByDesc('created_at');
            $refunds = $this->pagination($query, $request, $pagination);
            return view('admin.sales.refund.index', compact('refunds', 'pagination'));
        });
    }

    public function show(OrderRefund $refund)
    {
        return $this->try_catch_admin(function () use ($refund) {
            return view('admin.sales.refund.show', compact('refund'));
        });
    }


}
