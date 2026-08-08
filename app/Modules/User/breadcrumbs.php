<?php
declare(strict_types=1);

use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

////////////////////// CABINET & SHOP



////////////////////// ADMINS
//USERS
Breadcrumbs::for('admin.user.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Клиенты', route('admin.user.index'));
});
Breadcrumbs::for('admin.user.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.user.index');
    $trail->push($user->getPublicName(), route('admin.user.show', $user));
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
Breadcrumbs::for('admin.user.subscription.show', function (BreadcrumbTrail $trail, Subscription $subscription) {
    $trail->parent('admin.user.subscription.index');
    $trail->push($subscription->name, route('admin.user.subscription.show', $subscription));
});
