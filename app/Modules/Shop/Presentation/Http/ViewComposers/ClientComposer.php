<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class ClientComposer
{

    public function compose(View $view): void
    {
        $client = (auth()->check() && auth()->user()->isClient())
            ? auth()->user()->profileable
            : null;

        $view->with('client', $client);
    }
}
