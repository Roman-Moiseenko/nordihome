<?php
declare(strict_types=1);

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

//WORKER
Breadcrumbs::for('admin.worker.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Рабочие', route('admin.worker.index'));
});
Breadcrumbs::for('admin.worker.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.worker.index');
    $trail->push('Добавить нового', route('admin.worker.create'));
});
Breadcrumbs::for('admin.worker.show', function (BreadcrumbTrail $trail, Worker $worker) {
    $trail->parent('admin.worker.index');
    $trail->push($worker->fullname->getShortname(), route('admin.worker.show', $worker));
});
Breadcrumbs::for('admin.worker.edit', function (BreadcrumbTrail $trail, Worker $worker) {
    $trail->parent('admin.worker.show', $worker);
    $trail->push('Редактировать', route('admin.worker.edit', $worker));
});
