<?php
declare(strict_types=1);

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.login', function (BreadcrumbTrail $trail) {
    $trail->push('Login', route('admin.login'));
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
Breadcrumbs::for('admin.staff.security', function (BreadcrumbTrail $trail, Admin $staff) {
    $trail->parent('admin.staff.show', $staff);
    $trail->push('Сменить пароль', route('admin.staff.security', $staff));
});


Breadcrumbs::for('admin.staff.notification', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Уведомления', route('admin.staff.notification'));
});

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
