<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ReserveController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:order']);
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Product::orderBy('name')->Has('reserves');
            $products = $this->pagination($query, $request, $pagination);
            return view('admin.sales.reserve.index', compact('products', 'pagination'));
        });
    }
}
