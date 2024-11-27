<?php
declare(strict_types=1);

namespace App\Modules\Discount\Controllers;



use App\Http\Controllers\Controller;

class CouponController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth:admin', 'can:discount']);
    }

}
