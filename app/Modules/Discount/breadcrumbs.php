<?php
declare(strict_types=1);

use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Entity\Promotion;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// PROMOTION
Breadcrumbs::for('admin.discount.promotion.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Акции', route('admin.discount.promotion.index'));
});
Breadcrumbs::for('admin.discount.promotion.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push('Добавить новую', route('admin.discount.promotion.create'));
});
Breadcrumbs::for('admin.discount.promotion.show', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push($promotion->name, route('admin.discount.promotion.show', $promotion));
});
Breadcrumbs::for('admin.discount.promotion.edit', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.show', $promotion);
    $trail->push('Редактировать', route('admin.discount.promotion.edit', $promotion));
});
Breadcrumbs::for('admin.discount.promotion.update', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push($promotion->name, route('admin.discount.promotion.show', $promotion));
});

// DISCOUNT

Breadcrumbs::for('admin.discount.discount.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Скидки', route('admin.discount.discount.index'));
});
Breadcrumbs::for('admin.discount.discount.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.discount.discount.index');
    $trail->push('Добавить новую', route('admin.discount.discount.create'));
});
Breadcrumbs::for('admin.discount.discount.show', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.index');
    $trail->push($discount->name, route('admin.discount.discount.show', $discount));
});
Breadcrumbs::for('admin.discount.discount.edit', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.show', $discount);
    $trail->push('Редактировать', route('admin.discount.discount.edit', $discount));
});
Breadcrumbs::for('admin.discount.discount.update', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.index');
    $trail->push($discount->name, route('admin.discount.discount.show', $discount));
});
