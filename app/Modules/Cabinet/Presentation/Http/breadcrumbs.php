<?php

declare(strict_types=1);

use App\Modules\Catalog\Entity\Review;
use App\Modules\Order\Infrastructure\Models\Order;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Breadcrumbs for Cabinet module
|--------------------------------------------------------------------------
|
| Define your breadcrumbs using the Breadcrumbs::for() method.
|
| Example:
|
| Breadcrumbs::for('admin.cabinet.index', function (BreadcrumbTrail $trail) {
|     $trail->parent('admin.home');
|     $trail->push('Cabinet', route('admin.cabinet.index'));
| });
|
*/
//КАБИНЕТ
Breadcrumbs::for('cabinet', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Login', route('cabinet'));
});

Breadcrumbs::for('cabinet.view', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Мой кабинет', route('cabinet.view'));
});
Breadcrumbs::for('cabinet.wish.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Избранное', route('cabinet.wish.index'));
});
Breadcrumbs::for('cabinet.options.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Настройки', route('cabinet.options.index'));
});
Breadcrumbs::for('cabinet.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Мои заказы', route('cabinet.order.index'));
});
Breadcrumbs::for('cabinet.order.view', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('cabinet.order.index');
    $trail->push('Заказ ' . $order->htmlNum(), route('cabinet.order.view', $order));
});
Breadcrumbs::for('cabinet.order.new_order', function (BreadcrumbTrail $trail, int $id) {
    $order = Order::find($id);
    $trail->parent('cabinet.order.view', $order);
    $trail->push('Новый', route('cabinet.order.new_order', $order));
});
Breadcrumbs::for('cabinet.review.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Мои Отзывы', route('cabinet.review.index'));
});
Breadcrumbs::for('cabinet.review.show', function (BreadcrumbTrail $trail, Review $review) {
    $trail->parent('cabinet.review.index');
    $trail->push($review->product->name, route('cabinet.review.show', $review));
});

Breadcrumbs::for('login', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.home');
    $trail->push('Login', route('login'));
});
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('login');
    $trail->push('Register', route('register'));
});


Breadcrumbs::for('password.request', function (BreadcrumbTrail $trail) {
    $trail->parent('login');
    $trail->push('Reset Password', route('password.request'));
});

Breadcrumbs::for('other', function (BreadcrumbTrail $trail, $caption) {
    $trail->parent('login');
    $trail->push($caption, route('password.request'));
});
