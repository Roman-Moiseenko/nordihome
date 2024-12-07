<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Repository\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    private CartRepository $repository;

    public function __construct(CartRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
     /*   $query = Product::orderBy('name')->Has('cartStorages')->OrHas('cartCookies');
        $products = $this->pagination($query, $request, $pagination);*/
//return view('admin.user.cart.index', compact('products', 'pagination'));

        $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('User/Cart/Index', [
            'products' => $products,
        ]);
    }
}
