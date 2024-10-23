<?php
declare(strict_types=1);

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use App\Modules\Shop\Parser\ProductParser;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Attribute;

Breadcrumbs::for('admin.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Товары', route('admin.product.index'));
});

Breadcrumbs::for('admin.product.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Добавить новый', route('admin.product.create'));
});
Breadcrumbs::for('admin.product.show', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.product.index');
    $trail->push($product->name, route('admin.product.show', $product));
});
Breadcrumbs::for('admin.product.edit', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.product.show', $product);
    $trail->push('Редактировать', route('admin.product.edit', $product));
});
Breadcrumbs::for('admin.product.update', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.product.index');
    $trail->push($product->name, route('admin.product.show', $product));
});

//BRAND
Breadcrumbs::for('admin.product.brand.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Бренды', route('admin.product.brand.index'));
});
Breadcrumbs::for('admin.product.brand.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.brand.index');
    $trail->push('Добавить новый', route('admin.product.brand.create'));
});
Breadcrumbs::for('admin.product.brand.show', function (BreadcrumbTrail $trail, Brand $brand) {
    $trail->parent('admin.product.brand.index');
    $trail->push($brand->name, route('admin.product.brand.show', $brand));
});
Breadcrumbs::for('admin.product.brand.edit', function (BreadcrumbTrail $trail, Brand $brand) {
    $trail->parent('admin.product.brand.show', $brand);
    $trail->push('Редактировать', route('admin.product.brand.edit', $brand));
});

//CATEGORY
Breadcrumbs::for('admin.product.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Категории', route('admin.product.category.index'));
});
Breadcrumbs::for('admin.product.category.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.category.index');
    $trail->push('Добавить категорию', route('admin.product.category.create'));
});
Breadcrumbs::for('admin.product.category.child', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('admin.product.category.show', $category);
    $trail->push('Добавить подкатегорию', route('admin.product.category.create'));
});
Breadcrumbs::for('admin.product.category.show', function (BreadcrumbTrail $trail, Category $category) {
    if ($category->parent) {
        $trail->parent('admin.product.category.show', $category->parent);
    } else {
        $trail->parent('admin.product.category.index');
    }
    $trail->push($category->name, route('admin.product.category.show', $category));
});

Breadcrumbs::for('admin.product.category.edit', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('admin.product.category.show', $category);
    $trail->push('Редактировать', route('admin.product.category.edit', $category));
});
Breadcrumbs::for('admin.product.category.update', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('admin.product.category.index');
    $trail->push($category->name, route('admin.product.category.show', $category));
});

//ATTRIBUTE
Breadcrumbs::for('admin.product.attribute.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Атрибуты', route('admin.product.attribute.index'));
});
Breadcrumbs::for('admin.product.attribute.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.attribute.index');
    $trail->push('Добавить новый', route('admin.product.attribute.create'));
});
Breadcrumbs::for('admin.product.attribute.show', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.product.attribute.index');
    $trail->push($attribute->name, route('admin.product.attribute.show', $attribute));
});
Breadcrumbs::for('admin.product.attribute.edit', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.product.attribute.show', $attribute);
    $trail->push('Редактировать', route('admin.product.attribute.edit', $attribute));
});
Breadcrumbs::for('admin.product.attribute.update', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.product.attribute.index');
    $trail->push($attribute->name, route('admin.product.attribute.show', $attribute));
});

Breadcrumbs::for('admin.product.attribute.groups', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.attribute.index');
    $trail->push('Группы', route('admin.product.attribute.groups'));
});

//TAGS
Breadcrumbs::for('admin.product.tag.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Метки (Теги)', route('admin.product.tag.index'));
});

//EQUIVALENT
Breadcrumbs::for('admin.product.equivalent.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Аналоги', route('admin.product.equivalent.index'));
});
Breadcrumbs::for('admin.product.equivalent.show', function (BreadcrumbTrail $trail, Equivalent $equivalent) {
    $trail->parent('admin.product.equivalent.index');
    $trail->push($equivalent->name, route('admin.product.equivalent.show', $equivalent));
});

//GROUP
Breadcrumbs::for('admin.product.group.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Группы', route('admin.product.group.index'));
});
Breadcrumbs::for('admin.product.group.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.group.index');
    $trail->push('Добавить новую', route('admin.product.group.create'));
});
Breadcrumbs::for('admin.product.group.show', function (BreadcrumbTrail $trail, Group $group) {
    $trail->parent('admin.product.group.index');
    $trail->push($group->name, route('admin.product.group.show', $group));
});
Breadcrumbs::for('admin.product.group.edit', function (BreadcrumbTrail $trail, Group $group) {
    $trail->parent('admin.product.group.show', $group);
    $trail->push('Редактировать', route('admin.product.group.edit', $group));
});

//MODIFICATION
Breadcrumbs::for('admin.product.modification.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Модификации', route('admin.product.modification.index'));
});
Breadcrumbs::for('admin.product.modification.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.modification.index');
    $trail->push('Создать новую', route('admin.product.modification.create'));
});
Breadcrumbs::for('admin.product.modification.show', function (BreadcrumbTrail $trail, Modification $modification) {
    $trail->parent('admin.product.modification.index');
    $trail->push($modification->name, route('admin.product.modification.show', $modification));
});
Breadcrumbs::for('admin.product.modification.edit', function (BreadcrumbTrail $trail, Modification $modification) {
    $trail->parent('admin.product.modification.show', $modification);
    $trail->push('Редактировать', route('admin.product.modification.edit', $modification));
});

//PARSER PRODUCTS
Breadcrumbs::for('admin.product.parser.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Спарсенные товары', route('admin.product.parser.index'));
});
Breadcrumbs::for('admin.product.parser.show', function (BreadcrumbTrail $trail, ProductParser $productParser) {
    $trail->parent('admin.product.index');
    $trail->push($productParser->product->name, route('admin.product.parser.show', $productParser));
});

//SERIES
Breadcrumbs::for('admin.product.series.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Серии товаров', route('admin.product.series.index'));
});
Breadcrumbs::for('admin.product.series.show', function (BreadcrumbTrail $trail, Series $series) {
    $trail->parent('admin.product.series.index');
    $trail->push($series->name, route('admin.product.series.show', $series));
});
//PRIORITY
Breadcrumbs::for('admin.product.priority.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.product.index');
    $trail->push('Приоритетный показ товаров', route('admin.product.priority.index'));
});

