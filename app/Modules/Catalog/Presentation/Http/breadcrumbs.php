<?php
declare(strict_types=1);

use App\Modules\Catalog\Entity\Attribute;
use App\Modules\Catalog\Entity\Brand;
use App\Modules\Catalog\Entity\CategorySize;
use App\Modules\Catalog\Entity\Equivalent;
use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Entity\Modification;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Entity\Series;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Shop\Parser\ProductParser;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.catalog.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Товары', route('admin.catalog.product.index'));
});

Breadcrumbs::for('admin.catalog.product.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Добавить новый', route('admin.catalog.product.create'));
});
Breadcrumbs::for('admin.catalog.product.show', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.catalog.product.index');
    $trail->push($product->name, route('admin.catalog.product.show', $product));
});
Breadcrumbs::for('admin.catalog.product.edit', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.catalog.product.show', $product);
    $trail->push('Редактировать', route('admin.catalog.product.edit', $product));
});
Breadcrumbs::for('admin.catalog.product.update', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('admin.catalog.product.index');
    $trail->push($product->name, route('admin.catalog.product.show', $product));
});

//BRAND
Breadcrumbs::for('admin.catalog.brand.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Бренды', route('admin.catalog.brand.index'));
});
Breadcrumbs::for('admin.catalog.brand.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.brand.index');
    $trail->push('Добавить новый', route('admin.catalog.brand.create'));
});
Breadcrumbs::for('admin.catalog.brand.show', function (BreadcrumbTrail $trail, Brand $brand) {
    $trail->parent('admin.catalog.brand.index');
    $trail->push($brand->name, route('admin.catalog.brand.show', $brand));
});
Breadcrumbs::for('admin.catalog.brand.edit', function (BreadcrumbTrail $trail, Brand $brand) {
    $trail->parent('admin.catalog.brand.show', $brand);
    $trail->push('Редактировать', route('admin.catalog.brand.edit', $brand));
});

//CATEGORY
Breadcrumbs::for('admin.catalog.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Категории', route('admin.catalog.category.index'));
});
Breadcrumbs::for('admin.catalog.category.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.category.index');
    $trail->push('Добавить категорию', route('admin.catalog.category.create'));
});
Breadcrumbs::for('admin.catalog.category.child', function (BreadcrumbTrail $trail, int|string $id) {
    $trail->parent('admin.catalog.category.show', $id);
    $trail->push('Добавить подкатегорию', route('admin.catalog.category.create'));
});
Breadcrumbs::for('admin.catalog.category.show', function (BreadcrumbTrail $trail, int|string $id) {
    $categoryRepository = app(App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface::class);
    $category = $categoryRepository->getById((int) $id);
    if ($category->parentId) {
        $parentCategory = $categoryRepository->getById($category->parentId);
        $trail->parent('admin.catalog.category.show', $parentCategory->id);
    } else {
        $trail->parent('admin.catalog.category.index');
    }
    $trail->push($category->name, route('admin.catalog.category.show', $category->id));
});

Breadcrumbs::for('admin.catalog.category.edit', function (BreadcrumbTrail $trail, int|string $id) {
    $trail->parent('admin.catalog.category.show', $id);
    $trail->push('Редактировать', route('admin.catalog.category.edit', $id));
});
Breadcrumbs::for('admin.catalog.category.update', function (BreadcrumbTrail $trail, int|string $id) {
    $categoryRepository = app(App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface::class);
    $category = $categoryRepository->getById((int) $id);
    $trail->parent('admin.catalog.category.index');
    $trail->push($category->name, route('admin.catalog.category.show', $category->id));
});

//ATTRIBUTE
Breadcrumbs::for('admin.catalog.attribute.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Атрибуты', route('admin.catalog.attribute.index'));
});
Breadcrumbs::for('admin.catalog.attribute.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.attribute.index');
    $trail->push('Добавить новый', route('admin.catalog.attribute.create'));
});
Breadcrumbs::for('admin.catalog.attribute.show', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.catalog.attribute.index');
    $trail->push($attribute->name, route('admin.catalog.attribute.show', $attribute));
});
Breadcrumbs::for('admin.catalog.attribute.edit', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.catalog.attribute.show', $attribute);
    $trail->push('Редактировать', route('admin.catalog.attribute.edit', $attribute));
});
Breadcrumbs::for('admin.catalog.attribute.update', function (BreadcrumbTrail $trail, Attribute $attribute) {
    $trail->parent('admin.catalog.attribute.index');
    $trail->push($attribute->name, route('admin.catalog.attribute.show', $attribute));
});

Breadcrumbs::for('admin.catalog.attribute.groups', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.attribute.index');
    $trail->push('Группы', route('admin.catalog.attribute.groups'));
});

//TAGS
Breadcrumbs::for('admin.catalog.tag.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Метки (Теги)', route('admin.catalog.tag.index'));
});

//EQUIVALENT
Breadcrumbs::for('admin.catalog.equivalent.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Аналоги', route('admin.catalog.equivalent.index'));
});
Breadcrumbs::for('admin.catalog.equivalent.show', function (BreadcrumbTrail $trail, Equivalent $equivalent) {
    $trail->parent('admin.catalog.equivalent.index');
    $trail->push($equivalent->name, route('admin.catalog.equivalent.show', $equivalent));
});

//GROUP
Breadcrumbs::for('admin.catalog.group.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Группы', route('admin.catalog.group.index'));
});

Breadcrumbs::for('admin.catalog.group.show', function (BreadcrumbTrail $trail, Group $group) {
    $trail->parent('admin.catalog.group.index');
    $trail->push($group->name, route('admin.catalog.group.show', $group));
});

//MODIFICATION
Breadcrumbs::for('admin.catalog.modification.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Модификации', route('admin.catalog.modification.index'));
});
Breadcrumbs::for('admin.catalog.modification.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.modification.index');
    $trail->push('Создать новую', route('admin.catalog.modification.create'));
});
Breadcrumbs::for('admin.catalog.modification.show', function (BreadcrumbTrail $trail, Modification $modification) {
    $trail->parent('admin.catalog.modification.index');
    $trail->push($modification->name, route('admin.catalog.modification.show', $modification));
});
Breadcrumbs::for('admin.catalog.modification.edit', function (BreadcrumbTrail $trail, Modification $modification) {
    $trail->parent('admin.catalog.modification.show', $modification);
    $trail->push('Редактировать', route('admin.catalog.modification.edit', $modification));
});

//PARSER PRODUCTS
Breadcrumbs::for('admin.catalog.parser.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Спарсенные товары', route('admin.catalog.parser.index'));
});
Breadcrumbs::for('admin.catalog.parser.show', function (BreadcrumbTrail $trail, ProductParser $productParser) {
    $trail->parent('admin.catalog.product.index');
    $trail->push($productParser->product->name, route('admin.catalog.parser.show', $productParser));
});

//SERIES
Breadcrumbs::for('admin.catalog.series.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Серии товаров', route('admin.catalog.series.index'));
});
Breadcrumbs::for('admin.catalog.series.show', function (BreadcrumbTrail $trail, Series $series) {
    $trail->parent('admin.catalog.series.index');
    $trail->push($series->name, route('admin.catalog.series.show', $series));
});
//PRIORITY
Breadcrumbs::for('admin.catalog.priority.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Приоритетный показ товаров', route('admin.catalog.priority.index'));
});
//REDUCED
Breadcrumbs::for('admin.catalog.reduced.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Цена снижена на товар', route('admin.catalog.reduced.index'));
});
//ON-ORDER
Breadcrumbs::for('admin.catalog.on-order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Только под заказ', route('admin.catalog.on-order.index'));
});
//GROUP
Breadcrumbs::for('admin.catalog.size.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('Размеры', route('admin.catalog.size.index'));
});

Breadcrumbs::for('admin.catalog.size.show', function (BreadcrumbTrail $trail, CategorySize $category) {
    $trail->parent('admin.catalog.size.index');
    $trail->push($category->name, route('admin.catalog.size.show', $category));
});

//ROOMS
Breadcrumbs::for('admin.catalog.room.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.catalog.product.index');
    $trail->push('По комнатам', route('admin.catalog.room.index'));
});

Breadcrumbs::for('admin.catalog.room.show', function (BreadcrumbTrail $trail, int|string $id) {
    $roomRepository = app(App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface::class);
    $room = $roomRepository->getById((int) $id);
    $trail->parent('admin.catalog.room.index');
    $trail->push($room->name, route('admin.catalog.room.show', $room->id));
});
