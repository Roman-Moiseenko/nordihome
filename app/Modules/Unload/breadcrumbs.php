<?php
declare(strict_types=1);

use App\Modules\Unload\Entity\Feed;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.unload.feed.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Фиды', route('admin.unload.feed.index'));
});
Breadcrumbs::for('admin.unload.feed.show', function (BreadcrumbTrail $trail, Feed $feed) {
    $trail->parent('admin.unload.feed.index');
    $trail->push($feed->name, route('admin.unload.feed.show', $feed));
});

