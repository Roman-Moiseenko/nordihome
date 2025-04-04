<?php
declare(strict_types=1);

use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Widget;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


//WIDGET
Breadcrumbs::for('admin.page.widget.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты', route('admin.page.widget.index'));
});

Breadcrumbs::for('admin.page.widget.show', function (BreadcrumbTrail $trail, Widget $widget) {
    $trail->parent('admin.page.widget.index');
    $trail->push($widget->name, route('admin.page.widget.show', $widget));
});

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

//BANNER
Breadcrumbs::for('admin.page.banner.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Баннеры', route('admin.page.banner.index'));
});
Breadcrumbs::for('admin.page.banner.show', function (BreadcrumbTrail $trail, Banner $banner) {
    $trail->parent('admin.page.banner.index', $banner);
    $trail->push($banner->name, route('admin.page.banner.show', $banner));
});
