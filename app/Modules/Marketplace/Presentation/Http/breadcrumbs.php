<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.marketplace.avito.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Авито товары', route('admin.marketplace.avito.index'));
});

Breadcrumbs::for('admin.marketplace.avito.categories', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketplace.avito.index');
    $trail->push('Категории', route('admin.marketplace.avito.categories'));
});

Breadcrumbs::for('admin.marketplace.avito.settings', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketplace.avito.index');
    $trail->push('Настройки', route('admin.marketplace.avito.settings'));
});

Breadcrumbs::for('admin.marketplace.avito.operations', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketplace.avito.index');
    $trail->push('Операции', route('admin.marketplace.avito.operations'));
});

Breadcrumbs::for('admin.marketplace.avito.product.show', function (BreadcrumbTrail $trail, int|string $id) {
    $repository = app(\App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface::class);
    $product = $repository->getById((int) $id);

    $trail->parent('admin.marketplace.avito.index');
    $trail->push($product->name, route('admin.marketplace.avito.product.show', $product->id));
});
