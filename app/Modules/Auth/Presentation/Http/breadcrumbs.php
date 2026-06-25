<?php
declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// ============================================================
// STAFF (Сотрудники)
// ============================================================
Breadcrumbs::for('admin.staff.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Сотрудники', route('admin.staff.index'));
});

Breadcrumbs::for('admin.staff.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.staff.index');
    $trail->push('Добавить нового', route('admin.staff.create'));
});

Breadcrumbs::for('admin.staff.show', function (BreadcrumbTrail $trail, mixed $staff) {
    if (!$staff instanceof \App\Modules\Auth\Infrastructure\Models\Staff) {
        $staff = \App\Modules\Auth\Infrastructure\Models\Staff::findOrFail($staff);
    }
    $trail->parent('admin.staff.index');
    $trail->push($staff->fullName, route('admin.staff.show', $staff));
});

Breadcrumbs::for('admin.staff.edit', function (BreadcrumbTrail $trail, mixed $staff) {
    if (!$staff instanceof \App\Modules\Auth\Infrastructure\Models\Staff) {
        $staff = \App\Modules\Auth\Infrastructure\Models\Staff::findOrFail($staff);
    }
    $trail->parent('admin.staff.show', $staff);
    $trail->push('Редактировать', route('admin.staff.edit', $staff));
});

Breadcrumbs::for('admin.staff.security', function (BreadcrumbTrail $trail, mixed $staff) {
    if (!$staff instanceof \App\Modules\Auth\Infrastructure\Models\Staff) {
        $staff = \App\Modules\Auth\Infrastructure\Models\Staff::findOrFail($staff);
    }
    $trail->parent('admin.staff.show', $staff);
    $trail->push('Сменить пароль', route('admin.staff.security', $staff));
});

Breadcrumbs::for('admin.staff.notification', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Уведомления', route('admin.staff.notification'));
});

// ============================================================
// CLIENT (Клиенты)
// ============================================================
Breadcrumbs::for('admin.client.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Клиенты', route('admin.client.index'));
});

Breadcrumbs::for('admin.client.show', function (BreadcrumbTrail $trail, mixed $client) {
    if (!$client instanceof \App\Modules\Auth\Infrastructure\Models\Client) {
        $client = \App\Modules\Auth\Infrastructure\Models\Client::findOrFail($client);
    }
    $trail->parent('admin.client.index');
    $trail->push($client->fullName ?? $client->id, route('admin.client.show', $client));
});

Breadcrumbs::for('admin.client.edit', function (BreadcrumbTrail $trail, mixed $client) {
    if (!$client instanceof \App\Modules\Auth\Infrastructure\Models\Client) {
        $client = \App\Modules\Auth\Infrastructure\Models\Client::findOrFail($client);
    }
    $trail->parent('admin.client.show', $client);
    $trail->push('Редактировать', route('admin.client.edit', $client));
});

// ============================================================
// FREELANCE (Внештатные сотрудники)
// ============================================================
Breadcrumbs::for('admin.freelance.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Внештатные сотрудники', route('admin.freelance.index'));
});

Breadcrumbs::for('admin.freelance.show', function (BreadcrumbTrail $trail, mixed $freelance) {
    if (!$freelance instanceof \App\Modules\Auth\Infrastructure\Models\Freelance) {
        $freelance = \App\Modules\Auth\Infrastructure\Models\Freelance::findOrFail($freelance);
    }
    $trail->parent('admin.freelance.index');
    $trail->push($freelance->fullName ?? $freelance->id, route('admin.freelance.show', $freelance));
});

Breadcrumbs::for('admin.freelance.edit', function (BreadcrumbTrail $trail, mixed $freelance) {
    if (!$freelance instanceof \App\Modules\Auth\Infrastructure\Models\Freelance) {
        $freelance = \App\Modules\Auth\Infrastructure\Models\Freelance::findOrFail($freelance);
    }
    $trail->parent('admin.freelance.show', $freelance);
    $trail->push('Редактировать', route('admin.freelance.edit', $freelance));
});

// ============================================================
// ROLE (Управление ролями)
// ============================================================
Breadcrumbs::for('admin.role.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Роли', route('admin.role.index'));
});

Breadcrumbs::for('admin.role.show', function (BreadcrumbTrail $trail, mixed $role) {
    $trail->parent('admin.role.index');
    $trail->push($role->name ?? 'Роль', route('admin.role.show', $role));
});

Breadcrumbs::for('admin.role.edit', function (BreadcrumbTrail $trail, mixed $role) {
    $trail->parent('admin.role.show', $role);
    $trail->push('Редактировать', route('admin.role.edit', $role));
});


