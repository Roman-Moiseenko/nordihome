<?php
declare(strict_types=1);

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyDocument;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


//STORAGE

Breadcrumbs::for('admin.accounting.storage.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Хранилища', route('admin.accounting.storage.index'));
});
Breadcrumbs::for('admin.accounting.storage.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.storage.index');
    $trail->push('Добавить', route('admin.accounting.storage.create'));
});
Breadcrumbs::for('admin.accounting.storage.show', function (BreadcrumbTrail $trail, Storage $storage) {
    $trail->parent('admin.accounting.storage.index');
    $trail->push($storage->name, route('admin.accounting.storage.show', $storage));
});
Breadcrumbs::for('admin.accounting.storage.edit', function (BreadcrumbTrail $trail, Storage $storage) {
    $trail->parent('admin.accounting.storage.show', $storage);
    $trail->push('Редактировать', route('admin.accounting.storage.edit', $storage));
});
//DISTRIBUTOR
Breadcrumbs::for('admin.accounting.distributor.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Поставщики', route('admin.accounting.distributor.index'));
});
Breadcrumbs::for('admin.accounting.distributor.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.distributor.index');
    $trail->push('Добавить', route('admin.accounting.distributor.create'));
});
Breadcrumbs::for('admin.accounting.distributor.show', function (BreadcrumbTrail $trail, Distributor $distributor) {
    $trail->parent('admin.accounting.distributor.index');
    $trail->push($distributor->name, route('admin.accounting.distributor.show', $distributor));
});
Breadcrumbs::for('admin.accounting.distributor.edit', function (BreadcrumbTrail $trail, Distributor $distributor) {
    $trail->parent('admin.accounting.distributor.show', $distributor);
    $trail->push('Редактировать', route('admin.accounting.distributor.edit', $distributor));
});
//CURRENCY
Breadcrumbs::for('admin.accounting.currency.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Курсы валют', route('admin.accounting.currency.index'));
});
Breadcrumbs::for('admin.accounting.currency.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.currency.index');
    $trail->push('Добавить', route('admin.accounting.currency.create'));
});
Breadcrumbs::for('admin.accounting.currency.show', function (BreadcrumbTrail $trail, Currency $currency) {
    $trail->parent('admin.accounting.currency.index');
    $trail->push($currency->name, route('admin.accounting.currency.show', $currency));
});
Breadcrumbs::for('admin.accounting.currency.edit', function (BreadcrumbTrail $trail, Currency $currency) {
    $trail->parent('admin.accounting.currency.show', $currency);
    $trail->push('Редактировать', route('admin.accounting.currency.edit', $currency));
});
//ARRIVAL
Breadcrumbs::for('admin.accounting.arrival.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Поступление товаров', route('admin.accounting.arrival.index'));
});
Breadcrumbs::for('admin.accounting.arrival.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.arrival.index');
    $trail->push('Добавить', route('admin.accounting.arrival.create'));
});
Breadcrumbs::for('admin.accounting.arrival.show', function (BreadcrumbTrail $trail, ArrivalDocument $arrival) {
    $trail->parent('admin.accounting.arrival.index');
    $trail->push($arrival->number . ' от ' . $arrival->created_at->format('d-m-Y'), route('admin.accounting.arrival.show', $arrival));
});
Breadcrumbs::for('admin.accounting.arrival.edit', function (BreadcrumbTrail $trail, ArrivalDocument $arrival) {
    $trail->parent('admin.accounting.arrival.show', $arrival);
    $trail->push('Редактировать', route('admin.accounting.arrival.edit', $arrival));
});
//MOVEMENT
Breadcrumbs::for('admin.accounting.movement.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Перемещение товаров', route('admin.accounting.movement.index'));
});
Breadcrumbs::for('admin.accounting.movement.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.movement.index');
    $trail->push('Добавить', route('admin.accounting.movement.create'));
});
Breadcrumbs::for('admin.accounting.movement.show', function (BreadcrumbTrail $trail, MovementDocument $movement) {
    $trail->parent('admin.accounting.movement.index');
    $trail->push($movement->number . ' от ' . $movement->created_at->format('d-m-Y'), route('admin.accounting.movement.show', $movement));
});
Breadcrumbs::for('admin.accounting.movement.edit', function (BreadcrumbTrail $trail, MovementDocument $movement) {
    $trail->parent('admin.accounting.movement.show', $movement);
    $trail->push('Редактировать', route('admin.accounting.movement.edit', $movement));
});
//DEPARTURE
Breadcrumbs::for('admin.accounting.departure.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Списание товаров', route('admin.accounting.departure.index'));
});
Breadcrumbs::for('admin.accounting.departure.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.departure.index');
    $trail->push('Добавить', route('admin.accounting.departure.create'));
});
Breadcrumbs::for('admin.accounting.departure.show', function (BreadcrumbTrail $trail, DepartureDocument $departure) {
    $trail->parent('admin.accounting.departure.index');
    $trail->push($departure->number . ' от ' . $departure->created_at->format('d-m-Y'), route('admin.accounting.departure.show', $departure));
});
Breadcrumbs::for('admin.accounting.departure.edit', function (BreadcrumbTrail $trail, DepartureDocument $departure) {
    $trail->parent('admin.accounting.departure.show', $departure);
    $trail->push('Редактировать', route('admin.accounting.departure.edit', $departure));
});
//SUPPLY
Breadcrumbs::for('admin.accounting.supply.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Заказы товаров', route('admin.accounting.supply.index'));
});
Breadcrumbs::for('admin.accounting.supply.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.supply.index');
    $trail->push('Добавить', route('admin.accounting.supply.create'));
});
Breadcrumbs::for('admin.accounting.supply.show', function (BreadcrumbTrail $trail, SupplyDocument $supply) {
    $trail->parent('admin.accounting.supply.index');
    $trail->push($supply->number . ' от ' . $supply->created_at->format('d-m-Y'), route('admin.accounting.supply.show', $supply));
});
Breadcrumbs::for('admin.accounting.supply.edit', function (BreadcrumbTrail $trail, SupplyDocument $supply) {
    $trail->parent('admin.accounting.supply.show', $supply);
    $trail->push('Редактировать', route('admin.accounting.supply.edit', $supply));
});
Breadcrumbs::for('admin.accounting.supply.stack', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.supply.index');
    $trail->push('Стек заказов', route('admin.accounting.supply.stack'));
});
//PRICING
Breadcrumbs::for('admin.accounting.pricing.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Установка цен', route('admin.accounting.pricing.index'));
});
Breadcrumbs::for('admin.accounting.pricing.show', function (BreadcrumbTrail $trail, PricingDocument $pricing) {
    $trail->parent('admin.accounting.pricing.index');
    $trail->push($pricing->number . ' от ' . $pricing->created_at->format('d-m-Y'), route('admin.accounting.pricing.show', $pricing));
});

//ORGANIZATION
Breadcrumbs::for('admin.accounting.organization.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Организации', route('admin.accounting.organization.index'));
});
Breadcrumbs::for('admin.accounting.organization.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.accounting.organization.index');
    $trail->push('Добавить', route('admin.accounting.organization.create'));
});
Breadcrumbs::for('admin.accounting.organization.show', function (BreadcrumbTrail $trail, Organization $organization) {
    $trail->parent('admin.accounting.organization.index');
    $trail->push($organization->name, route('admin.accounting.organization.show', $organization));
});
Breadcrumbs::for('admin.accounting.organization.edit', function (BreadcrumbTrail $trail, Organization $organization) {
    $trail->parent('admin.accounting.organization.show', $organization);
    $trail->push('Редактировать', route('admin.accounting.organization.edit', $organization));
});
