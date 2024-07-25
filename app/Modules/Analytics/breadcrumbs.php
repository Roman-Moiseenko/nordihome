<?php
declare(strict_types=1);

use App\Modules\Analytics\Entity\LoggerCron;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.analytics.activity.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Логгер действий сотрудников', route('admin.analytics.activity.index'));
});
Breadcrumbs::for('admin.analytics.cron.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Логгер действий по расписанию', route('admin.analytics.cron.index'));
});
Breadcrumbs::for('admin.analytics.cron.show', function (BreadcrumbTrail $trail, LoggerCron $cron) {
    $trail->parent('admin.home');
    $trail->push($cron->event, route('admin.analytics.cron.show', $cron));
});
