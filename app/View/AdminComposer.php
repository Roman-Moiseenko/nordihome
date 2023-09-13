<?php
declare(strict_types=1);

namespace App\View;

use App\Menu\AdminMenu;
use Illuminate\View\View;

class AdminComposer
{
    public function compose(View $view): void
    {
        if (!is_null(request()->route())) {
            $pageName = request()->route()->getName();
            $layout = $view->layout ?? '';
            $activeMenu = $this->activeMenu($pageName, $layout);
            if ($layout == 'admin') {
                $view->with('sideMenu', AdminMenu::menu());
                $view->with('firstLevelActiveIndex', $activeMenu['first_level_active_index']);
                $view->with('secondLevelActiveIndex', $activeMenu['second_level_active_index']);
                $view->with('thirdLevelActiveIndex', $activeMenu['third_level_active_index']);
            }
        }
    }
    public function activeMenu($pageName, $layout): array
    {
        $firstLevelActiveIndex = '';
        $secondLevelActiveIndex = '';
        $thirdLevelActiveIndex = '';
        if ($layout == 'admin')
        foreach (AdminMenu::menu() as $menuKey => $menu) {
            if ($menu !== 'divider' && isset($menu['route_name']) && $menu['route_name'] == $pageName && empty($firstPageName)) {
                $firstLevelActiveIndex = $menuKey;
            }

            if (isset($menu['sub_menu'])) {
                foreach ($menu['sub_menu'] as $subMenuKey => $subMenu) {
                    if (isset($subMenu['route_name']) && $subMenu['route_name'] == $pageName && $menuKey != 'menu-layout' && empty($secondPageName)) {
                        $firstLevelActiveIndex = $menuKey;
                        $secondLevelActiveIndex = $subMenuKey;
                    }

                    if (isset($subMenu['sub_menu'])) {
                        foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu) {
                            if (isset($lastSubMenu['route_name']) && $lastSubMenu['route_name'] == $pageName) {
                                $firstLevelActiveIndex = $menuKey;
                                $secondLevelActiveIndex = $subMenuKey;
                                $thirdLevelActiveIndex = $lastSubMenuKey;
                            }
                        }
                    }
                }
            }
        }
        return [
            'first_level_active_index' => $firstLevelActiveIndex,
            'second_level_active_index' => $secondLevelActiveIndex,
            'third_level_active_index' => $thirdLevelActiveIndex
        ];
    }
}
