<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class WishController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:order']);
    }

    public function index(Request $request)
    {
        $query = Product::orderBy('name')->Has('wishes');
        $products = $this->pagination($query, $request, $pagination);
        return view('admin.user.wish.index', compact('products', 'pagination'));
    }
}
