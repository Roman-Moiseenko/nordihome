<?php
declare(strict_types=1);

use App\Modules\Content\Entity\Contact;
use App\Modules\Content\Entity\Gallery;
use App\Modules\Content\Entity\News;
use App\Modules\Content\Entity\Page;
use App\Modules\Content\Entity\Post;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Entity\Widgets\BannerWidget;
use App\Modules\Content\Entity\Widgets\FormWidget;
use App\Modules\Content\Entity\Widgets\PostWidget;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\PromotionWidget;
use App\Modules\Content\Entity\Widgets\TextWidget;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


//////WIDGETS//////

//PRODUCT
Breadcrumbs::for('admin.content.widget.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты товаров', route('admin.content.widget.product.index'));
});

Breadcrumbs::for('admin.content.widget.product.show', function (BreadcrumbTrail $trail, ProductWidget $widget) {
    $trail->parent('admin.content.widget.product.index');
    $trail->push($widget->name, route('admin.content.widget.product.show', $widget));
});
//BANNER
Breadcrumbs::for('admin.content.widget.banner.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Баннеры (Виджет)', route('admin.content.widget.banner.index'));
});
Breadcrumbs::for('admin.content.widget.banner.show', function (BreadcrumbTrail $trail, BannerWidget $widget) {
    $trail->parent('admin.content.widget.banner.index', $widget);
    $trail->push($widget->name, route('admin.content.widget.banner.show', $widget));
});
//PROMOTION
Breadcrumbs::for('admin.content.widget.promotion.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты Акции', route('admin.content.widget.promotion.index'));
});
Breadcrumbs::for('admin.content.widget.promotion.show', function (BreadcrumbTrail $trail, PromotionWidget $widget) {
    $trail->parent('admin.content.widget.promotion.index', $widget);
    $trail->push($widget->name, route('admin.content.widget.promotion.show', $widget));
});
//TEXT
Breadcrumbs::for('admin.content.widget.text.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Текстовые Виджеты', route('admin.content.widget.text.index'));
});
Breadcrumbs::for('admin.content.widget.text.show', function (BreadcrumbTrail $trail, TextWidget $widget) {
    $trail->parent('admin.content.widget.text.index', $widget);
    $trail->push($widget->name, route('admin.content.widget.text.show', $widget));
});

//FORM
Breadcrumbs::for('admin.content.widget.form.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты обратной связи', route('admin.content.widget.form.index'));
});
Breadcrumbs::for('admin.content.widget.form.show', function (BreadcrumbTrail $trail, FormWidget $widget) {
    $trail->parent('admin.content.widget.form.index', $widget);
    $trail->push($widget->name, route('admin.content.widget.form.show', $widget));
});

//POST
Breadcrumbs::for('admin.content.widget.post.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Виджеты записей', route('admin.content.widget.post.index'));
});
Breadcrumbs::for('admin.content.widget.post.show', function (BreadcrumbTrail $trail, PostWidget $widget) {
    $trail->parent('admin.content.widget.post.index', $widget);
    $trail->push($widget->name, route('admin.content.widget.post.show', $widget));
});
//////WIDGETS//////

//CACHE
Breadcrumbs::for('admin.content.cache.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Кеш страниц', route('admin.content.cache.index'));
});


//PAGE
Breadcrumbs::for('admin.content.page.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Страницы', route('admin.content.page.index'));
});
Breadcrumbs::for('admin.content.page.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.content.page.index');
    $trail->push('Добавить новую', route('admin.content.page.create'));
});
Breadcrumbs::for('admin.content.page.show', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.content.page.index');
    $trail->push($page->name, route('admin.content.page.show', $page));
});
Breadcrumbs::for('admin.content.page.edit', function (BreadcrumbTrail $trail, Page $page) {
    $trail->parent('admin.content.page.show', $page);
    $trail->push('Редактировать', route('admin.content.page.edit', $page));
});
//CONTACT
Breadcrumbs::for('admin.content.contact.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Контакты', route('admin.content.contact.index'));
});
Breadcrumbs::for('admin.content.contact.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.content.contact.index');
    $trail->push('Добавить новый', route('admin.content.contact.create'));
});

Breadcrumbs::for('admin.content.contact.edit', function (BreadcrumbTrail $trail, Contact $contact) {
    $trail->parent('admin.content.contact.index', $contact);
    $trail->push($contact->name . ' - Редактировать', route('admin.content.page.edit', $contact));
});

//NEWS
Breadcrumbs::for('admin.content.news.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Новости', route('admin.content.news.index'));
});
Breadcrumbs::for('admin.content.news.show', function (BreadcrumbTrail $trail, News $news) {
    $trail->parent('admin.content.news.index', $news);
    $trail->push($news->title, route('admin.content.news.show', $news));
});
//POST CATEGORIES
Breadcrumbs::for('admin.content.post-category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Рубрики', route('admin.content.post-category.index'));
});
Breadcrumbs::for('admin.content.post-category.show', function (BreadcrumbTrail $trail, PostCategory $category) {
    $trail->parent('admin.content.post-category.index', $category);
    $trail->push($category->name, route('admin.content.post-category.show', $category));
});
//POSTS
Breadcrumbs::for('admin.content.post.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Записи', route('admin.content.post.index'));
});
Breadcrumbs::for('admin.content.post.show', function (BreadcrumbTrail $trail, Post $post) {
    $trail->parent('admin.content.post-category.show', $post->category);
    $trail->push($post->name, route('admin.content.post.show', $post));
});
//MENUS
Breadcrumbs::for('admin.content.menu.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Меню', route('admin.content.menu.index'));
});
//GALLERY
Breadcrumbs::for('admin.content.gallery.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Галерея', route('admin.content.gallery.index'));
});
Breadcrumbs::for('admin.content.gallery.show', function (BreadcrumbTrail $trail, Gallery $gallery) {
    $trail->parent('admin.content.gallery.index');
    $trail->push($gallery->name, route('admin.content.gallery.show', $gallery));
});
//SEO META
Breadcrumbs::for('admin.content.seo-meta.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('SEO META', route('admin.content.seo-meta.index'));
});
