<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

if (config('shop.theme') != 'nbrussia') return;

Breadcrumbs::for('admin.nbrussia.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('NB Russia', route('admin.nbrussia.index'));
});

Breadcrumbs::for('admin.nbrussia.parser.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('NB Russia Парсер', route('admin.nbrussia.parser.index'));
});

