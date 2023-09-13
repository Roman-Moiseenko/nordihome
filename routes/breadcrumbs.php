<?php
declare(strict_types=1);

use App\Entity\User\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
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

Breadcrumbs::for('admin.home', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Admin', route('admin.home'));
});

Breadcrumbs::for('admin.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Users', route('admin.users.index'));
});
Breadcrumbs::for('admin.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users.index');
    $trail->push('Create', route('admin.users.create'));
});
Breadcrumbs::for('admin.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->name, route('admin.users.show', $user));
});
Breadcrumbs::for('admin.users.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.users.show', $user);
    $trail->push('Edit', route('admin.users.edit', $user));
});

Breadcrumbs::for('admin.users.update', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->name, route('admin.users.show', $user));
});
Breadcrumbs::for('admin.login', function (BreadcrumbTrail $trail) {
    $trail->push('Login', route('admin.login'));
});




/**  Пример рекурсии для вложенных категорий и товара */
Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category) { //Без указания главной - home
    if ($category->parent) {
        $trail->parent('category', $category->parent);
    }
    $trail->parent('category');
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

