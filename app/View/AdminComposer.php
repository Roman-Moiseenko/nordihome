<?php
declare(strict_types=1);

namespace App\View;

use App\Modules\Base\Helpers\AdminMenu;
use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Shop\Repository\MenuRepository;
use Cache;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Repository\ShopRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminComposer
{
    private CategoryRepository $categories;
    private ShopRepository $shopRepository;
    private Settings $settings;
    private MenuRepository $menuRepository;

    public function __construct(CategoryRepository $categories, ShopRepository $shopRepository, Settings $settings, MenuRepository $menuRepository)
    {
        $this->categories = $categories;
        $this->shopRepository = $shopRepository;
        $this->settings = $settings;
        $this->menuRepository = $menuRepository;
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
                // $admin = Auth::guard('admin')->user();
                //  $view->with('admin', $admin);
            } elseif ($layout == 'livewire') {
                //
            } else {


                //TODO Определение местоположения
                /*$token = env('DADATA_TOKEN', false);
                if ($token) {
                    $dadata = new \Dadata\DadataClient($token, null);
                    $result = $dadata->iplocate(request()->ip());
                    $city = $result['data']['city'];
                } else {
                    $city = 'Лунапарк';
                }*/
                //Категории в кеше
                /*     if (!Cache::has(CacheHelper::MENU_CATEGORIES))
                         Cache::put(CacheHelper::MENU_CATEGORIES, $this->shopRepository->getChildren());

                     if (!Cache::has(CacheHelper::MENU_TREES))
                         Cache::put(CacheHelper::MENU_TREES, $this->shopRepository->getTree());



                     $user = (Auth::guard('user')->check()) ? Auth::guard('user')->user() : null;
                     $view->with('user', $user);
                    // $view->with('config', config('shop.frontend'));
                   //  $city = 'Россия';

                     $view->with('url_page', request()->url());
                     //$view->with('city', $city);

                 } */
             /*   $categories = Cache::rememberForever(CacheHelper::MENU_CATEGORIES, function () {
                    return $this->shopRepository->getChildren();
                });
                $trees = Cache::rememberForever(CacheHelper::MENU_TREES, function () {
                    return $this->shopRepository->getTree();
                });

*/

                /**
                 * Глобальные данные для клиентской части
                 */

                $user = (Auth::guard('user')->check()) ? Auth::guard('user')->user() : null;
                $view->with('user', $user);
                $view->with('config', config('shop.frontend'));

                $view->with('contacts', $this->menuRepository->contacts());
                $view->with('menus', $this->menuRepository->menus());

                $categories = Cache::rememberForever(CacheHelper::MENU_CATEGORIES, function () {
                    return $this->shopRepository->getChildren();
                });
                $view->with('url_page', request()->url());
                $trees = Cache::rememberForever(CacheHelper::MENU_TREES, function () {
                    return $this->shopRepository->getTree();
                });

                $view->with('categories', $categories);
                $view->with('tree', $trees);
                $view->with('web', $this->settings->web);
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

}
