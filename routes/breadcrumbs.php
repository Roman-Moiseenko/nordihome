<?php
declare(strict_types=1);

use App\Entity\Admin;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Widget;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ProductParser;
use App\Modules\Shop\ShopRepository;
use App\Modules\User\Entity\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/**  S H O P */

//TODO Перенести в отдельный файл ????

/**  Пример рекурсии для вложенных категорий и товара */
Breadcrumbs::for('shop.category.index', function (BreadcrumbTrail $trail) { //Без указания главной - home
    $trail->parent('home');
    $trail->push('Каталог', route('shop.category.index'));
});

Breadcrumbs::for('shop.category.view', function (BreadcrumbTrail $trail, $slug) { //Без указания главной - home
    $category = (new ShopRepository())->CategoryBySlug($slug);
    if (is_null($category)) {
        $trail->parent('shop.category.index');
        $trail->push('Категория не найдена');
    } else {
        if ($category->parent) {
            $trail->parent('shop.category.view', $category->parent_id);
        } else {
            $trail->parent('shop.category.index');
        }
        $trail->push($category->name, route('shop.category.view', $category->slug));
    }

});


//Выводим магазины в крошках, с родительской - Home
/*Breadcrumbs::for('shop', function (BreadcrumbTrail $trail, $shop) {
    $trail->parent('home');
    $trail->push($shop->name, route('shop'));
});*/
//Для товара собираем из предыдущих
Breadcrumbs::for('shop.product.view', function (BreadcrumbTrail $trail, $slug) {
    $product = (new ShopRepository())->getProductBySlug($slug);
    //$trail->parent('shop', $product->shop); //Крошка - Home > Магазин xxx >
    if (is_null($product)) {
        $trail->parent('shop.category.index');
        $trail->push('Товар не найден'); // Крошка - Товар

    } else {
        $trail->parent('shop.category.view', $product->main_category_id); //Крошка - Категория > Подкатегория >
        $trail->push($product->name, route('shop.product.view', $product->slug)); // Крошка - Товар
        }
});
//****/
Breadcrumbs::for('errors.404', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Страница не найдена');
});


Breadcrumbs::for('shop.cart.view', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Корзина', route('shop.cart.view'));
});
Breadcrumbs::for('shop.order.create', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.cart.view');
    $trail->push('Оформить заказ', route('shop.order.create'));
});
Breadcrumbs::for('shop.order.create-parser', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.parser.view');
    $trail->push('Оформить заказ', route('shop.order.create-parser'));
});

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('<i class="fa-light fa-house"></i>', route('home'));
});

Breadcrumbs::for('login', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Login', route('login'));
});
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('login');
    $trail->push('Register', route('register'));
});
Breadcrumbs::for('cabinet', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Login', route('cabinet'));
});

Breadcrumbs::for('password.request', function (BreadcrumbTrail $trail) {
    $trail->parent('login');
    $trail->push('Reset Password', route('password.request'));
});

Breadcrumbs::for('other', function (BreadcrumbTrail $trail, $caption) {
    $trail->parent('login');
    $trail->push($caption, route('password.request'));
});

Breadcrumbs::for('shop.page.view', function (BreadcrumbTrail $trail, $slug) {
    $page = (new ShopRepository())->PageBySlug($slug);
    $trail->parent('home');
    $trail->push($page->name, route('shop.page.view', $slug));
});

Breadcrumbs::for('shop.parser.view', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Заказ товаров с каталога IKEA.PL', route('shop.parser.view'));
});

Breadcrumbs::for('shop.promotion.view', function (BreadcrumbTrail $trail, $slug) {
    $promotion = (new ShopRepository())->getPromotionBySlug($slug);
    $trail->parent('home');
    $trail->push($promotion->title, route('shop.promotion.view', $slug));
});

//КАБИНЕТ

Breadcrumbs::for('cabinet.view', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Мой кабинет', route('cabinet.view'));
});
Breadcrumbs::for('cabinet.wish.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Избранное', route('cabinet.wish.index'));
});
Breadcrumbs::for('cabinet.options.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Настройки', route('cabinet.options.index'));
});
Breadcrumbs::for('cabinet.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('cabinet.view');
    $trail->push('Мои заказы', route('cabinet.order.index'));
});
Breadcrumbs::for('cabinet.order.view', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('cabinet.order.index');
    $trail->push('Заказ ' . $order->htmlNum(), route('cabinet.order.view', $order));
});
/**  A D M I N  */


Breadcrumbs::for('admin.home', function (BreadcrumbTrail $trail) {
    $trail->push('CRM', route('admin.home'));
});

//USERS
Breadcrumbs::for('admin.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Клиенты', route('admin.users.index'));
});
Breadcrumbs::for('admin.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->email, route('admin.users.show', $user));
});
//STAFF
Breadcrumbs::for('admin.staff.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Сотрудники', route('admin.staff.index'));
});
Breadcrumbs::for('admin.staff.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.staff.index');
    $trail->push('Добавить нового', route('admin.staff.create'));
});
Breadcrumbs::for('admin.staff.show', function (BreadcrumbTrail $trail, Admin $staff) {
    $trail->parent('admin.staff.index');
    $trail->push($staff->fullname->getShortname(), route('admin.staff.show', $staff));
});
Breadcrumbs::for('admin.staff.edit', function (BreadcrumbTrail $trail, Admin $staff) {
    $trail->parent('admin.staff.show', $staff);
    $trail->push('Редактировать', route('admin.staff.edit', $staff));
});

Breadcrumbs::for('admin.staff.update', function (BreadcrumbTrail $trail, Admin $staff) {
    $trail->parent('admin.staff.index');
    $trail->push($staff->fullname->getShortname(), route('admin.staff.show', $staff));
});
Breadcrumbs::for('admin.staff.security', function (BreadcrumbTrail $trail, Admin $staff) {
    $trail->parent('admin.staff.show', $staff);
    $trail->push('Сменить пароль', route('admin.staff.security', $staff));
});

///// *** SHOP
// PRODUCTS
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

///// *** DISCOUNT
// PROMOTION
Breadcrumbs::for('admin.discount.promotion.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Акции', route('admin.discount.promotion.index'));
});
Breadcrumbs::for('admin.discount.promotion.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push('Добавить новую', route('admin.discount.promotion.create'));
});
Breadcrumbs::for('admin.discount.promotion.show', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push($promotion->name, route('admin.discount.promotion.show', $promotion));
});
Breadcrumbs::for('admin.discount.promotion.edit', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.show', $promotion);
    $trail->push('Редактировать', route('admin.discount.promotion.edit', $promotion));
});
Breadcrumbs::for('admin.discount.promotion.update', function (BreadcrumbTrail $trail, Promotion $promotion) {
    $trail->parent('admin.discount.promotion.index');
    $trail->push($promotion->name, route('admin.discount.promotion.show', $promotion));
});

// DISCOUNT
Breadcrumbs::for('admin.discount.discount.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Скидки', route('admin.discount.discount.index'));
});
Breadcrumbs::for('admin.discount.discount.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.discount.discount.index');
    $trail->push('Добавить новую', route('admin.discount.discount.create'));
});
Breadcrumbs::for('admin.discount.discount.show', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.index');
    $trail->push($discount->name, route('admin.discount.discount.show', $discount));
});
Breadcrumbs::for('admin.discount.discount.edit', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.show', $discount);
    $trail->push('Редактировать', route('admin.discount.discount.edit', $discount));
});
Breadcrumbs::for('admin.discount.discount.update', function (BreadcrumbTrail $trail, Discount $discount) {
    $trail->parent('admin.discount.discount.index');
    $trail->push($discount->name, route('admin.discount.discount.show', $discount));
});

//DELIVERY
Breadcrumbs::for('admin.delivery.all', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Доставка', route('admin.delivery.all'));
});
Breadcrumbs::for('admin.delivery.local', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Доставка по региону', route('admin.delivery.local'));
});
Breadcrumbs::for('admin.delivery.region', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Доставка ТК', route('admin.delivery.region'));
});
Breadcrumbs::for('admin.delivery.storage', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.delivery.all');
    $trail->push('Самовывоз', route('admin.delivery.storage'));
});

//SALES
Breadcrumbs::for('admin.sales', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Продажи', route('admin.home')); //TODO Заменить
});

Breadcrumbs::for('admin.sales.cart.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Корзина', route('admin.sales.cart.index'));
});

Breadcrumbs::for('admin.sales.reserve.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Резерв', route('admin.sales.reserve.index'));
});

Breadcrumbs::for('admin.sales.wish.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Избранное', route('admin.sales.wish.index'));
});

Breadcrumbs::for('admin.sales.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Заказы новые', route('admin.sales.order.index'));
});

Breadcrumbs::for('admin.sales.order.show', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.sales.order.index');
    $trail->push($order->htmlDate() . ' ' . $order->htmlNum(), route('admin.sales.order.show', $order));
});

Breadcrumbs::for('admin.sales.preorder.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Предзаказы новые', route('admin.sales.preorder.index'));
});
Breadcrumbs::for('admin.sales.preorder.show', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.sales.preorder.index');
    $trail->push($order->htmlDate() . ' ' . $order->htmlNum(), route('admin.sales.preorder.show', $order));
});

Breadcrumbs::for('admin.sales.executed.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Заказы завершенные', route('admin.sales.executed.index'));
});
Breadcrumbs::for('admin.sales.executed.show', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.sales.executed.index');
    $trail->push($order->htmlDate() . ' ' . $order->htmlNum(), route('admin.sales.executed.show', $order));
});
Breadcrumbs::for('admin.login', function (BreadcrumbTrail $trail) {
    $trail->push('Login', route('admin.login'));
});

/////ACCOUNTING
//STORAGE
Breadcrumbs::for('admin.accounting.storage.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Хранилища', route('admin.accounting.storage.index'));
});
Breadcrumbs::for('admin.accounting.storage.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.storage.index');
    $trail->push('Добавить', route('admin.accounting.storage.create'));
});
Breadcrumbs::for('admin.accounting.storage.show', function (BreadcrumbTrail $trail, Storage $storage) {
    $trail->parent('admin.accounting.storage.index');
    $trail->push($storage->name, route('admin.accounting.storage.show', $storage));
});
Breadcrumbs::for('admin.accounting.storage.edit', function (BreadcrumbTrail $trail, Storage $storage) {
    $trail->parent('admin.accounting.storage.show', $storage);
    $trail->push('Редактировать', route('admin.accounting.storage.edit', $storage));
});
//DISTRIBUTOR
Breadcrumbs::for('admin.accounting.distributor.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Поставщики', route('admin.accounting.distributor.index'));
});
Breadcrumbs::for('admin.accounting.distributor.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.distributor.index');
    $trail->push('Добавить', route('admin.accounting.distributor.create'));
});
Breadcrumbs::for('admin.accounting.distributor.show', function (BreadcrumbTrail $trail, Distributor $distributor) {
    $trail->parent('admin.accounting.distributor.index');
    $trail->push($distributor->name, route('admin.accounting.distributor.show', $distributor));
});
Breadcrumbs::for('admin.accounting.distributor.edit', function (BreadcrumbTrail $trail, Distributor $distributor) {
    $trail->parent('admin.accounting.distributor.show', $distributor);
    $trail->push('Редактировать', route('admin.accounting.distributor.edit', $distributor));
});
//CURRENCY
Breadcrumbs::for('admin.accounting.currency.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Курсы валют', route('admin.accounting.currency.index'));
});
Breadcrumbs::for('admin.accounting.currency.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.currency.index');
    $trail->push('Добавить', route('admin.accounting.currency.create'));
});
Breadcrumbs::for('admin.accounting.currency.show', function (BreadcrumbTrail $trail, Currency $currency) {
    $trail->parent('admin.accounting.currency.index');
    $trail->push($currency->name, route('admin.accounting.currency.show', $currency));
});
Breadcrumbs::for('admin.accounting.currency.edit', function (BreadcrumbTrail $trail, Currency $currency) {
    $trail->parent('admin.accounting.currency.show', $currency);
    $trail->push('Редактировать', route('admin.accounting.currency.edit', $currency));
});
//ARRIVAL
Breadcrumbs::for('admin.accounting.arrival.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Поступление товаров', route('admin.accounting.arrival.index'));
});
Breadcrumbs::for('admin.accounting.arrival.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.arrival.index');
    $trail->push('Добавить', route('admin.accounting.arrival.create'));
});
Breadcrumbs::for('admin.accounting.arrival.show', function (BreadcrumbTrail $trail, ArrivalDocument $arrival) {
    $trail->parent('admin.accounting.arrival.index');
    $trail->push($arrival->number . ' от ' . $arrival->created_at->format('d-m-Y'), route('admin.accounting.arrival.show', $arrival));
});
Breadcrumbs::for('admin.accounting.arrival.edit', function (BreadcrumbTrail $trail, ArrivalDocument $arrival) {
    $trail->parent('admin.accounting.arrival.show', $arrival);
    $trail->push('Редактировать', route('admin.accounting.arrival.edit', $arrival));
});


/////PAGES
//WIDGET
Breadcrumbs::for('admin.page.widget.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты', route('admin.page.widget.index'));
});
Breadcrumbs::for('admin.page.widget.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.page.widget.index');
    $trail->push('Добавить новый', route('admin.page.widget.create'));
});
Breadcrumbs::for('admin.page.widget.show', function (BreadcrumbTrail $trail, Widget $widget) {
    $trail->parent('admin.page.widget.index');
    $trail->push($widget->name, route('admin.page.widget.show', $widget));
});
Breadcrumbs::for('admin.page.widget.edit', function (BreadcrumbTrail $trail, Widget $widget) {
    $trail->parent('admin.page.widget.show', $widget);
    $trail->push('Редактировать', route('admin.page.widget.edit', $widget));
});
Breadcrumbs::for('admin.page.widget.update', function (BreadcrumbTrail $trail, Widget $widget) {
    $trail->parent('admin.page.widget.index');
    $trail->push($widget->name, route('admin.page.widget.show', $widget));
});
//PAGE
Breadcrumbs::for('admin.page.page.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Страницы', route('admin.page.page.index'));
});
Breadcrumbs::for('admin.page.page.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.page.page.index');
    $trail->push('Добавить новый', route('admin.page.page.create'));
});
Breadcrumbs::for('admin.page.page.show', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.page.page.index');
    $trail->push($page->name, route('admin.page.page.show', $page));
});
Breadcrumbs::for('admin.page.page.edit', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.page.page.show', $page);
    $trail->push('Редактировать', route('admin.page.page.edit', $page));
});
Breadcrumbs::for('admin.page.page.update', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.page.page.index');
    $trail->push($page->name, route('admin.page.page.show', $page));
});


Breadcrumbs::for('admin.settings.shop', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Интернет Магазин', route('admin.settings.shop'));
});

/**  Пример рекурсии для вложенных категорий и товара */
/*
Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category) { //Без указания главной - home
    if ($category->parent) {
        $trail->parent('category', $category->parent);
    } else {
        $trail->parent('shop');
    }
    $trail->push($category->name, route('category', $category));
});
//Выводим магазины в крошках, с родительской - Home
Breadcrumbs::for('shop', function (BreadcrumbTrail $trail, $shop) {
    $trail->parent('home');
    $trail->push($shop->name, route('shop'));
});
//Для товара собираем из предыдущих
Breadcrumbs::for('product', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('shop', $product->shop); //Крошка - Home > Магазин xxx >
    $trail->parent('category', $product->parent); //Крошка - Категория > Подкатегория >
    $trail->push($product->name, route('product')); // Крошка - Товар
});

*/
