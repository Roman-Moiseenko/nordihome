<?php
declare(strict_types=1);

use App\Modules\Parser\Entity\CategoryParser;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
Breadcrumbs::for('admin.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Товары', route('admin.product.index'));
});

Breadcrumbs::for('admin.product.show', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.product.index');
    $trail->push($product->name, route('admin.product.show', $product));
});
Breadcrumbs::for('admin.product.edit', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.product.show', $product);
    $trail->push('Редактировать', route('admin.product.edit', $product));
});

*/

//CATEGORY
Breadcrumbs::for('admin.parser.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Категории', route('admin.parser.category.index'));
});


Breadcrumbs::for('admin.parser.category.show', function (BreadcrumbTrail $trail, CategoryParser $category) {
    if ($category->parent) {
        $trail->parent('admin.parser.category.show', $category->parent);
    } else {
        $trail->parent('admin.parser.category.index');
    }
    $trail->push($category->name, route('admin.parser.category.show', $category));
});

