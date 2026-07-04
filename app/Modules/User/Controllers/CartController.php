<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Repository\CartRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    private CartRepository $repository;

    public function __construct(CartRepository $repository)
    {
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
