<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use Illuminate\View\View;

class WebComposer
{
    public function compose(View $view): void
    {
        $view->with('url_page', request()->url());
        $view->with('config', config('shop.frontend'));
    }
}
