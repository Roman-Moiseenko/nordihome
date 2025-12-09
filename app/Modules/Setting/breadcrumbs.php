<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


Breadcrumbs::for('admin.setting.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Настройки', route('admin.setting.index'));
});



Breadcrumbs::for('admin.setting.parser', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Парсер', route('admin.setting.parser'));
});
Breadcrumbs::for('admin.setting.common', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Общие', route('admin.setting.common'));
});

Breadcrumbs::for('admin.setting.coupon', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Купоны', route('admin.setting.coupon'));
});
Breadcrumbs::for('admin.setting.web', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Сайт', route('admin.setting.web'));
});
Breadcrumbs::for('admin.setting.mail', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Почта', route('admin.setting.mail'));
});
Breadcrumbs::for('admin.setting.notification', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Уведомления', route('admin.setting.notification'));
});
Breadcrumbs::for('admin.setting.image', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.setting.index');
    $trail->push('Изображения', route('admin.setting.image'));
});
