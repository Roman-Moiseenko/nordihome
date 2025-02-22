<?php
declare(strict_types=1);

use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Repository\SlugRepository;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/** @var Settings $settings */
$settings = app()->make(Settings::class);

Breadcrumbs::for('shop.home', function (BreadcrumbTrail $trail) use ($settings) {
    $trail->push($settings->web->breadcrumbs_home, route('shop.home'));
});

/**  S H O P */
/**  Пример рекурсии для вложенных категорий и товара */
Breadcrumbs::for('shop.category.index', function (BreadcrumbTrail $trail) { //Без указания главной - home
    $trail->parent('shop.home');
    $trail->push('Каталог', route('shop.category.index'));
});

Breadcrumbs::for('shop.category.view', function (BreadcrumbTrail $trail, $slug) use ($settings) { //Без указания главной - home
   // \Log::info('shop.category.view ' .$slug);
    $category = (new SlugRepository())->CategoryBySlug($slug);
    if (is_null($category)) {
        if ($settings->web->is_category) $trail->parent('shop.category.index');
        $trail->push('Категория не найдена');
    } else {
        if ($category->parent) {
            $trail->parent('shop.category.view', $category->parent_id);
        } else {
            if ($settings->web->is_category) {
                $trail->parent('shop.category.index');
            } else {
                $trail->parent('shop.home');
            }
        }
        $trail->push($category->name, route('shop.category.view', $category->slug));
    }
});

//Для товара собираем из предыдущих
Breadcrumbs::for('shop.product.view', function (BreadcrumbTrail $trail, $slug) {
    $product = (new SlugRepository())->getProductBySlug($slug);
    //$trail->parent('shop', $product->shop); //Крошка - Home > Магазин xxx >
    if (is_null($product)) {
        $trail->parent('shop.category.index');
        $trail->push('Товар не найден'); // Крошка - Товар

    } else {
        $trail->parent('shop.category.view', $product->main_category_id); //Крошка - Категория > Подкатегория >
        $trail->push($product->name, route('shop.product.view', $product->slug)); // Крошка - Товар
    }
});
//Черновик
Breadcrumbs::for('shop.product.view-draft', function (BreadcrumbTrail $trail, Product $product) {
    if (is_null($product->main_category_id)) {
        $trail->parent('shop.category.index');
    } else {
        $trail->parent('shop.category.view', $product->main_category_id);
    }
    $trail->push($product->name, route('shop.product.view-draft', $product)); // Крошка - Товар
});

Breadcrumbs::for('shop.page.view', function (BreadcrumbTrail $trail, $slug) {
    $page = (new SlugRepository())->PageBySlug($slug);
    $trail->parent('shop.home');
    $trail->push($page->name, route('shop.page.view', $slug));
});

Breadcrumbs::for('shop.parser.view', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Заказ товаров с каталога IKEA.PL', route('shop.parser.view'));
});

Breadcrumbs::for('shop.promotion.view', function (BreadcrumbTrail $trail, $slug) {
    $promotion = (new SlugRepository())->getPromotionBySlug($slug);
    $trail->parent('shop.home');
    $trail->push('Акция ' . $promotion->title, route('shop.promotion.view', $slug));
});

Breadcrumbs::for('shop.group.view', function (BreadcrumbTrail $trail, $slug) {
    $group = (new SlugRepository())->getGroupBySlug($slug);
    $trail->parent('shop.home');
    $trail->push('Группа товаров ' . $group->name, route('shop.group.view', $slug));
});

Breadcrumbs::for('errors.404', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Страница не найдена');
});

Breadcrumbs::for('shop.cart.view', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Корзина', route('shop.cart.view'));
});
Breadcrumbs::for('shop.order.create', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.cart.view');
    $trail->push('Оформить заказ', route('shop.order.create'));
});
Breadcrumbs::for('shop.order.create-parser', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.parser.view');
    $trail->push('Оформить заказ', route('shop.order.create-parser'));
});
