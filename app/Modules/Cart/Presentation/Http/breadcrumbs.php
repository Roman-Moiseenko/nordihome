<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Breadcrumbs for Cart module
|--------------------------------------------------------------------------
|
| Define your breadcrumbs using the Breadcrumbs::for() method.
|
| Example:
|
| Breadcrumbs::for('admin.cart.index', function (BreadcrumbTrail $trail) {
|     $trail->parent('admin.home');
|     $trail->push('Cart', route('admin.cart.index'));
| });
|
*/
