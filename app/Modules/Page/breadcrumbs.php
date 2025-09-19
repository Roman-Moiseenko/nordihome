<?php
declare(strict_types=1);

use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\ProductWidget;
use App\Modules\Page\Entity\PromotionWidget;
use App\Modules\Page\Entity\TextWidget;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


//////WIDGETS//////

//PRODUCT
Breadcrumbs::for('admin.page.widget.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты товаров', route('admin.page.widget.product.index'));
});

Breadcrumbs::for('admin.page.widget.product.show', function (BreadcrumbTrail $trail, ProductWidget $widget) {
    $trail->parent('admin.page.widget.product.index');
    $trail->push($widget->name, route('admin.page.widget.product.show', $widget));
});
//BANNER
Breadcrumbs::for('admin.page.widget.banner.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Баннеры (Виджет)', route('admin.page.widget.banner.index'));
});
Breadcrumbs::for('admin.page.widget.banner.show', function (BreadcrumbTrail $trail, BannerWidget $banner) {
    $trail->parent('admin.page.widget.banner.index', $banner);
    $trail->push($banner->name, route('admin.page.widget.banner.show', $banner));
});
//PROMOTION
Breadcrumbs::for('admin.page.widget.promotion.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты Акции', route('admin.page.widget.promotion.index'));
});
Breadcrumbs::for('admin.page.widget.promotion.show', function (BreadcrumbTrail $trail, PromotionWidget $promotion) {
    $trail->parent('admin.page.widget.promotion.index', $promotion);
    $trail->push($promotion->name, route('admin.page.widget.promotion.show', $promotion));
});
//TEXT
Breadcrumbs::for('admin.page.widget.text.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Текстовые Виджеты', route('admin.page.widget.text.index'));
});
Breadcrumbs::for('admin.page.widget.text.show', function (BreadcrumbTrail $trail, TextWidget $text) {
    $trail->parent('admin.page.widget.text.index', $text);
    $trail->push($text->name, route('admin.page.widget.text.show', $text));
});

//////WIDGETS//////

//CACHE
Breadcrumbs::for('admin.page.cache.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Кеш страниц', route('admin.page.cache.index'));
});


//PAGE
Breadcrumbs::for('admin.page.page.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Страницы', route('admin.page.page.index'));
});
Breadcrumbs::for('admin.page.page.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.page.page.index');
    $trail->push('Добавить новую', route('admin.page.page.create'));
});
Breadcrumbs::for('admin.page.page.show', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.page.page.index');
    $trail->push($page->name, route('admin.page.page.show', $page));
});
Breadcrumbs::for('admin.page.page.edit', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.page.page.show', $page);
    $trail->push('Редактировать', route('admin.page.page.edit', $page));
});
//CONTACT
Breadcrumbs::for('admin.page.contact.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Контакты', route('admin.page.contact.index'));
});
Breadcrumbs::for('admin.page.contact.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.page.contact.index');
    $trail->push('Добавить новый', route('admin.page.contact.create'));
});

Breadcrumbs::for('admin.page.contact.edit', function (BreadcrumbTrail $trail, Contact $contact) {
    $trail->parent('admin.page.contact.index', $contact);
    $trail->push($contact->name . ' - Редактировать', route('admin.page.page.edit', $contact));
});

