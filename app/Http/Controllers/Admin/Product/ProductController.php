<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:commodity']);
    }
    public function index()
    {
        return view('admin.home');
    }
}
