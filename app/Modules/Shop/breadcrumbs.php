<?php
declare(strict_types=1);

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\ShopRepository;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('shop.home', function (BreadcrumbTrail $trail) {
    $trail->push('<i class="fa-light fa-house"></i>', route('shop.home'));
});

/**  S H O P */
/**  Пример рекурсии для вложенных категорий и товара */
Breadcrumbs::for('shop.category.index', function (BreadcrumbTrail $trail) { //Без указания главной - home
    $trail->parent('shop.home');
    $trail->push('Каталог', route('shop.category.index'));
});

Breadcrumbs::for('shop.category.view', function (BreadcrumbTrail $trail, $slug) { //Без указания главной - home
    $category = (new ShopRepository())->CategoryBySlug($slug);
    if (is_null($category)) {
        $trail->parent('shop.category.index');
        $trail->push('Категория не найдена');
    } else {
        if ($category->parent) {
            $trail->parent('shop.category.view', $category->parent_id);
        } else {
            $trail->parent('shop.category.index');
        }
        $trail->push($category->name, route('shop.category.view', $category->slug));
    }
});

//Для товара собираем из предыдущих
Breadcrumbs::for('shop.product.view', function (BreadcrumbTrail $trail, $slug) {
    $product = (new ShopRepository())->getProductBySlug($slug);
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
    $page = (new ShopRepository())->PageBySlug($slug);
    $trail->parent('shop.home');
    $trail->push($page->name, route('shop.page.view', $slug));
});

Breadcrumbs::for('shop.parser.view', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Заказ товаров с каталога IKEA.PL', route('shop.parser.view'));
});

Breadcrumbs::for('shop.promotion.view', function (BreadcrumbTrail $trail, $slug) {
    $promotion = (new ShopRepository())->getPromotionBySlug($slug);
    $trail->parent('shop.home');
    $trail->push($promotion->title, route('shop.promotion.view', $slug));
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
