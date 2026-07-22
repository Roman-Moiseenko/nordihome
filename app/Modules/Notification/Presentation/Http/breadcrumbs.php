<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Breadcrumbs for Notification module
|--------------------------------------------------------------------------
|
| Define your breadcrumbs using the Breadcrumbs::for() method.
|
| Example:
|
| Breadcrumbs::for('admin.notification.index', function (BreadcrumbTrail $trail) {
|     $trail->parent('admin.home');
|     $trail->push('Notification', route('admin.notification.index'));
| });
|
*/
Breadcrumbs::for('admin.notification.notification.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Мои уведомления', route('admin.notification.notification.index'));
});

Breadcrumbs::for('admin.notification.notification.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.notification.notification.index');
    $trail->push('Создать Уведомление', route('admin.notification.notification.create'));
});
