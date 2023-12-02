<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Discount;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DiscountController extends Controller
{

    public function index(Request $request)
    {
        $discounts = [];
        return view('admin.discount.discount.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discount.discount.create');
    }

}
