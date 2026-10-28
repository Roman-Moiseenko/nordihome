<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.analytics.dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Аналитика', route('admin.analytics.dashboard'));
});

Breadcrumbs::for('admin.analytics.searches', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Поиск', route('admin.analytics.searches'));
});

Breadcrumbs::for('admin.analytics.pages', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Страницы', route('admin.analytics.pages'));
});

Breadcrumbs::for('admin.analytics.actions', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Действия', route('admin.analytics.actions'));
});

Breadcrumbs::for('admin.analytics.sources', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Источники', route('admin.analytics.sources'));
});

Breadcrumbs::for('admin.analytics.audience', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Аудитория', route('admin.analytics.audience'));
});

Breadcrumbs::for('admin.analytics.funnel', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.analytics.dashboard');
    $trail->push('Воронка', route('admin.analytics.funnel'));
});
