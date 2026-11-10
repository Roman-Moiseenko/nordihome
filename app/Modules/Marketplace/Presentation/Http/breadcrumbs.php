<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Breadcrumbs for Marketplace module
|--------------------------------------------------------------------------
|
| Define your breadcrumbs using the Breadcrumbs::for() method.
|
| Example:
|
| Breadcrumbs::for('admin.marketplace.index', function (BreadcrumbTrail $trail) {
|     $trail->parent('admin.home');
|     $trail->push('Marketplace', route('admin.marketplace.index'));
| });
|
*/

Breadcrumbs::for('admin.marketplace.avito.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Авито товары', route('admin.marketplace.avito.index'));
});
