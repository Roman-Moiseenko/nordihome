<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

if (config('shop.theme') != 'nordihome') return;


Breadcrumbs::for('admin.nordihome.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Норди Хоум', route('admin.nordihome.index'));
});

