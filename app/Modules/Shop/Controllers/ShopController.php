<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Setting\Entity\Web;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\User\Entity\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class ShopController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected string $theme;
    public Web $web;
    protected ?User $user;

    public function __construct()
    {
        $this->middleware(['guest', 'guest:user']);
        if (Auth::guard('admin')->check()) {
            Auth::logout();
        }
        if (Auth::guard('user')->check()) {
            $this->user = Auth::guard('user')->user();
        } else {
            $this->user = null;
        }
        //$options = new Options();
       // config();
        $settings = new SettingRepository();
        $this->web = $settings->getWeb();
        $this->theme = config('shop.theme'); // $options->shop->theme;
    }

    final public function route(string $blade): string
    {
        return 'shop.' . $this->theme . '.' . $blade;
    }
}
