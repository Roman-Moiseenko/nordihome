<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class ShopController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected string $theme;

    public function __construct()
    {
        $this->middleware(['guest', 'guest:user']);
        if (Auth::guard('admin')->check()) {
            Auth::logout();
        }
        //$options = new Options();
       // config();
        $this->theme = config('shop.theme'); // $options->shop->theme;
    }

    final public function route(string $blade): string
    {
        return 'shop.' . $this->theme . '.' . $blade;
    }
}
