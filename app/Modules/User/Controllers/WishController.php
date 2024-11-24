<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Repository\WishRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class WishController extends Controller
{
    private WishRepository $repository;

    public function __construct(WishRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:order']);
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
 /*       $query = Product::orderBy('name')->Has('wishes');
        $products = $this->pagination($query, $request, $pagination);
        return view('admin.user.wish.index', compact('products', 'pagination'));
*/
        $products = $this->repository->getIndex($request, $filters);

        return Inertia::render('User/Wish/Index', [
            'products' => $products,
        ]);

    }
}
