<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.guide.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Справочники', route('admin.guide.index'));
});
Breadcrumbs::for('admin.guide.addition.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('Доп.услуги', route('admin.guide.addition.index'));
});
Breadcrumbs::for('admin.guide.country.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('Страны', route('admin.guide.country.index'));
});
Breadcrumbs::for('admin.guide.marking-type.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('Маркировка продукции', route('admin.guide.marking-type.index'));
});
Breadcrumbs::for('admin.guide.measuring.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('Единицы измерения', route('admin.guide.measuring.index'));
});
Breadcrumbs::for('admin.guide.vat.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('НДС', route('admin.guide.vat.index'));
});

Breadcrumbs::for('admin.guide.cargo-company.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.guide.index');
    $trail->push('Транспортные компании', route('admin.guide.cargo-company.index'));
});
