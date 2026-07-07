<?php

declare(strict_types=1);

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserLog;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


//CATEGORY
Breadcrumbs::for('admin.parser.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Категории Икеа', route('admin.parser.category.index'));
});
Breadcrumbs::for('admin.parser.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Товары Икеа', route('admin.parser.product.index'));
});

Breadcrumbs::for('admin.parser.category.show', function (BreadcrumbTrail $trail, ParserCategory $category_parser) {
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
Breadcrumbs::for('admin.parser.log.show', function (BreadcrumbTrail $trail, int $id) {
    $parser_log = ParserLog::find($id);
    $trail->parent('admin.parser.log.index');
    $trail->push($parser_log->date, route('admin.parser.log.show', $parser_log));
});
