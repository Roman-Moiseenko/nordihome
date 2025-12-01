<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.lead.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Текущие лиды', route('admin.lead.index'));
});

