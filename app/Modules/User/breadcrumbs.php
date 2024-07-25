<?php
declare(strict_types=1);

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Entity\Review;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

////////////////////// CABINET & SHOP

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


////////////////////// ADMINS
//USERS
Breadcrumbs::for('admin.user.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Клиенты', route('admin.user.index'));
});
Breadcrumbs::for('admin.user.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.user.index');
    $trail->push($user->email, route('admin.user.show', $user));
});


Breadcrumbs::for('admin.user.cart.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user.index');
    $trail->push('Корзина', route('admin.user.cart.index'));
});
Breadcrumbs::for('admin.user.wish.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user.index');
    $trail->push('Избранное', route('admin.user.wish.index'));
});

//SUBSCRIPTION
Breadcrumbs::for('admin.user.subscription.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user.index');
    $trail->push('Рассылки/Уведомления', route('admin.user.subscription.index'));
});
Breadcrumbs::for('admin.user.subscription.edit', function (BreadcrumbTrail $trail, Subscription $subscription) {
    $trail->parent('admin.user.subscription.index');
    $trail->push($subscription->name .' - Редактировать', route('admin.user.subscription.edit', $subscription));
});
