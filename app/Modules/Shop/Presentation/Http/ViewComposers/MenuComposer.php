<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Repository\MenuRepository;
use Illuminate\View\View;


readonly class MenuComposer
{
    public function __construct(
        private MenuRepository $menuRepository
    ) {}

    public function compose(View $view): void
    {
        $view->with('contacts', $this->menuRepository->contacts());
        $view->with('menus', $this->menuRepository->menus());
    }
}
