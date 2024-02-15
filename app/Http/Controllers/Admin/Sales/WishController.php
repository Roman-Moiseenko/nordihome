<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Sales;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
        try {
            $query = Product::orderBy('name')->Has('wishes');
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $products = $query->paginate($pagination);
                $products->appends(['p' => $pagination]);
            } else {
                $products = $query->paginate($this->pagination);
            }
            return view('admin.sales.wish.index', compact('products', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
