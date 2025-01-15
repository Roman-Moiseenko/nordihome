<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Modules\Notification\Entity\Notification;

Breadcrumbs::for('admin.notification.notification.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Мои уведомления', route('admin.notification.notification.index'));
});

Breadcrumbs::for('admin.notification.notification.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.notification.notification.index');
    $trail->push('Создать Уведомление', route('admin.notification.notification.create'));
});

