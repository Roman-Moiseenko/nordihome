<?php
declare(strict_types=1);

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ParserLog;
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
    $trail->push('Категории Парсера', route('admin.parser.category.index'));
});


Breadcrumbs::for('admin.parser.category.show', function (BreadcrumbTrail $trail, CategoryParser $category_parser) {
    if ($category_parser->parent) {
        $trail->parent('admin.parser.category.show', $category_parser->parent);
    } else {
        $trail->parent('admin.parser.category.index');
    }
    $trail->push($category_parser->name, route('admin.parser.category.show', $category_parser));
});

Breadcrumbs::for('admin.parser.log.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('История Парсера', route('admin.parser.log.index'));
});
Breadcrumbs::for('admin.parser.log.show', function (BreadcrumbTrail $trail, ParserLog $parser_log) {
    $trail->parent('admin.parser.log.index');
    $trail->push($parser_log->date, route('admin.parser.log.show', $parser_log));
});
