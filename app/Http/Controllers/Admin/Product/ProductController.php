<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
    }
    public function index(Request $request)
    {
        return view('admin.home');
    }
}
