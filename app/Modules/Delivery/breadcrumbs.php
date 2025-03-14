<?php
declare(strict_types=1);

use App\Modules\Delivery\Entity\DeliveryTruck;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

//DELIVERY
Breadcrumbs::for('admin.delivery.to-loader', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('На сборку', route('admin.delivery.to-loader'));
});
Breadcrumbs::for('admin.delivery.to-delivery', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('На доставку', route('admin.delivery.to-delivery'));
});



Breadcrumbs::for('admin.delivery.all', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Доставка', route('admin.delivery.all'));
});
/*
Breadcrumbs::for('admin.delivery.local', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Доставка по региону', route('admin.delivery.local'));
});
Breadcrumbs::for('admin.delivery.region', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Доставка ТК', route('admin.delivery.region'));
});
Breadcrumbs::for('admin.delivery.storage', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Самовывоз', route('admin.delivery.storage'));
});
*/

//TRUCK
Breadcrumbs::for('admin.delivery.truck.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Транспорт', route('admin.delivery.truck.index'));
});

//CALENDAR
/*
Breadcrumbs::for('admin.delivery.calendar.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Календарь доставки', route('admin.delivery.calendar.index'));
});
Breadcrumbs::for('admin.delivery.calendar.schedule', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.calendar.index');
    $trail->push('График доставки', route('admin.delivery.calendar.schedule'));
});
*/
