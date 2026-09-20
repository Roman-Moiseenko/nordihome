<?php

declare(strict_types=1);

use App\Modules\Output\Infrastructure\Models\Feed;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Breadcrumbs for Output module
|--------------------------------------------------------------------------
|
| Define your breadcrumbs using the Breadcrumbs::for() method.
|
| Example:
|
| Breadcrumbs::for('admin.output.index', function (BreadcrumbTrail $trail) {
|     $trail->parent('admin.home');
|     $trail->push('Output', route('admin.output.index'));
| });
|
*/
Breadcrumbs::for('admin.output.feed.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Фиды', route('admin.output.feed.index'));
});
Breadcrumbs::for('admin.output.feed.show', function (BreadcrumbTrail $trail, int $id) {
    $feed = Feed::find($id);
    $trail->parent('admin.output.feed.index');
    $trail->push($feed->name, route('admin.output.feed.show', $feed));
});
