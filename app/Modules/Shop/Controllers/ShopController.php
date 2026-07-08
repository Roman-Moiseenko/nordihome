<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class ShopController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public Web $web;
    protected ?Client $client;

    public function __construct()
    {
        //$this->middleware(['guest', 'guest:user']);
        //if (auth()->check()) Auth::logout();

        if (auth()->check() && auth()->user()->isClient()) {
            $this->client = auth()->user()->profileable;
        } else {
            $this->client = null;
        }

        $settings = app()->make(Settings::class);
        $this->web = $settings->web;
    }

    final public function route(string $blade): string
    {
        return 'shop.' . $blade;
    }
}
