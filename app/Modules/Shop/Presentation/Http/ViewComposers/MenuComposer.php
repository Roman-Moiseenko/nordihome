<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Application\Queries\Menu\GetContactsQuery;
use App\Modules\Shop\Application\Queries\Menu\GetMenusQuery;
use App\Modules\Shop\Repository\MenuRepository;
use Illuminate\View\View;

readonly class MenuComposer
{
    public function __construct(
        private  GetContactsQuery $getContactsQuery,
        private GetMenusQuery $getMenusQuery
    ) {}

    public function compose(View $view): void
    {
        $view->with('contacts', $this->getContactsQuery->execute());
        $view->with('menus', $this->getMenusQuery->execute());
    }
}
