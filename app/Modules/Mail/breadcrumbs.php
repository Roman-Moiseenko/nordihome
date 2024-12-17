<?php

use App\Modules\Mail\Entity\Inbox;
use App\Modules\Mail\Entity\Outbox;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use App\Modules\Mail\Entity\SystemMail;

//Системная
Breadcrumbs::for('admin.mail.system.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Системная почта', route('admin.mail.system.index'));
});
Breadcrumbs::for('admin.mail.system.show', function (BreadcrumbTrail $trail, SystemMail $mail) {
    $trail->parent('admin.mail.system.index');
    $trail->push($mail->user->fullname->getShortName(), route('admin.mail.system.show', $mail));
});
//Входящая
Breadcrumbs::for('admin.mail.inbox.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Входящая почта', route('admin.mail.inbox.index'));
});
Breadcrumbs::for('admin.mail.inbox.show', function (BreadcrumbTrail $trail, Inbox $mail) {
    $trail->parent('admin.mail.inbox.index');
    $trail->push($mail->email . ' * ' . $mail->subject, route('admin.mail.inbox.show', $mail));
});
//Исходящая
Breadcrumbs::for('admin.mail.outbox.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Исходящая почта', route('admin.mail.outbox.index'));
});
Breadcrumbs::for('admin.mail.outbox.show', function (BreadcrumbTrail $trail, Outbox $mail) {
    $trail->parent('admin.mail.outbox.index');
    $trail->push($mail->subject, route('admin.mail.outbox.show', $mail));
});
Breadcrumbs::for('admin.mail.outbox.edit', function (BreadcrumbTrail $trail, Outbox $mail) {
    $trail->parent('admin.mail.outbox.show', $mail);
    $trail->push('Редактировать', route('admin.mail.outbox.edit', $mail));
});

Breadcrumbs::for('admin.mail.outbox.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.mail.outbox.index');
    $trail->push('Создать письмо', route('admin.mail.outbox.create'));
});
