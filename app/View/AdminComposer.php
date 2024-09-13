<?php
declare(strict_types=1);

namespace App\View;

use App\Modules\Base\Helpers\AdminMenu;
use App\Menu\AdminProfileMenu;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Shop\MenuHelper;
use App\Modules\Shop\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class AdminComposer
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function compose(View $view): void
    {
        if (!is_null(request()->route())) {
            $pageName = request()->route()->getName();
            if ($pageName == null) {
                $layout = 'admin';
            } else {
                $layout = (str_contains($pageName, '.')) ? substr($pageName, 0, strpos($pageName, '.')) : 'shop';
            }
            $activeMenu = $this->activeMenu($pageName, $layout);
            if ($layout == 'admin') {
                $admin = Auth::guard('admin')->user();

                $view->with('sideMenu', AdminMenu::menu());
                $view->with('profileMenu', AdminProfileMenu::menu());
                $view->with('firstLevelActiveIndex', $activeMenu['first_level_active_index']);
                $view->with('secondLevelActiveIndex', $activeMenu['second_level_active_index']);
                $view->with('thirdLevelActiveIndex', $activeMenu['third_level_active_index']);
                $view->with('admin', $admin);
            } elseif($layout == 'livewire') {
                //
            } else {
                if ($layout == 'shop') {
                    $view->with('schema', new Schema());
                }
                //TODO Определение местоположения
                /*$token = env('DADATA_TOKEN', false);
                if ($token) {
                    $dadata = new \Dadata\DadataClient($token, null);
                    $result = $dadata->iplocate(request()->ip());
                    $city = $result['data']['city'];
                } else {
                    $city = 'Лунапарк';
                }*/
                $user = (Auth::guard('user')->check()) ? Auth::guard('user')->user() : null;
                $view->with('user', $user);
                $view->with('config', Config::get('shop-config.frontend'));
                $city = 'Калининград';

                $view->with('categories', $this->categories->getTree());
                $view->with('city', $city);
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
                if ($menu !== 'divider' && isset($menu['route_name']) && $this->checkRouteName($menu, $pageName) && empty($firstPageName)) {
                    $firstLevelActiveIndex = $menuKey;
                }

                if (isset($menu['sub_menu'])) {
                    foreach ($menu['sub_menu'] as $subMenuKey => $subMenu) {
                        if (isset($subMenu['route_name']) && $this->checkRouteName($subMenu, $pageName) && $menuKey != 'menu-layout' && empty($secondPageName)) {
                            $firstLevelActiveIndex = $menuKey;
                            $secondLevelActiveIndex = $subMenuKey;
                        }

                        if (isset($subMenu['sub_menu'])) {
                            foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu) {
                                if (isset($lastSubMenu['route_name']) && $this->checkRouteName($lastSubMenu, $pageName)) {
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

    private function checkRouteName($menu, $pageName): bool
    {

        if (isset($menu['action'])) return $menu['route_name'] == $pageName;
        return $this->clearAction($menu['route_name']) == $this->clearAction($pageName);
    }

    private function clearAction($str): string
    {
        if ($str == null) return '';
        $pos = strrpos($str, '.');
        $str = substr($str, 0, $pos);
        return $str;
    }

    /*
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
     * */
}
