<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Setting\Entity\Settings;
use Illuminate\View\View;

class WebComposer
{
    public function __construct(private  readonly Settings $service)
    {

    }
    public function compose(View $view): void
    {
        $view->with('web', $this->service->getWeb());
        $view->with('config', config('shop.frontend'));
    }
}
