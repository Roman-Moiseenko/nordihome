<?php

namespace App\Modules\User\Controllers\Cabinet;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\User\Entity\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class AuthCabinetController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public Web $web;
    protected ?User $user;

    public function __construct()
    {
        $this->middleware(['auth:user']);

        $this->user = Auth::guard('web')->user();

        $settings = app()->make(Settings::class);
        $this->web = $settings->web;
    }

    final public function route(string $blade): string
    {
        return 'shop.' . $blade;
    }
}
