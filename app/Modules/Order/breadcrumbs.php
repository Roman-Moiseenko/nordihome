<?php
declare(strict_types=1);

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderRefund;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Заказы', route('admin.order.index'));
});


Breadcrumbs::for('admin.order.reserve.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales');
    $trail->push('Резерв', route('admin.order.reserve.index'));
});


Breadcrumbs::for('admin.order.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.order.index');
    $trail->push('Новый заказ', route('admin.order.create'));
});

Breadcrumbs::for('admin.order.update', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.order.index');
    $trail->push($order->htmlDate() . ' ' . $order->htmlNum(), route('admin.order.update', $order));
});

Breadcrumbs::for('admin.order.show', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.order.index');
    $trail->push($order->htmlDate() . ' ' . $order->htmlNum(), route('admin.order.show', $order));
});

Breadcrumbs::for('admin.order.log', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('admin.order.show', $order);
    $trail->push('Лог заказа', route('admin.order.log', $order));
});

//EXPENSE
Breadcrumbs::for('admin.order.expense.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Расходники', route('admin.order.expense.index'));
});

Breadcrumbs::for('admin.order.expense.show', function (BreadcrumbTrail $trail, OrderExpense $expense) {
    $trail->parent('admin.order.show', $expense->order);
    $trail->push('Расходный документ', route('admin.order.expense.show', $expense));
});

//PAYMENT
Breadcrumbs::for('admin.order.payment.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Платежи', route('admin.order.payment.index'));
});
Breadcrumbs::for('admin.order.payment.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.order.payment.index');
    $trail->push('Новый платеж', route('admin.order.payment.create'));
});
Breadcrumbs::for('admin.order.payment.edit', function (BreadcrumbTrail $trail, OrderPayment $payment) {
    $trail->parent('admin.order.payment.show', $payment);
    $trail->push('Изменить', route('admin.order.payment.edit', $payment));
});
Breadcrumbs::for('admin.order.payment.show', function (BreadcrumbTrail $trail, OrderPayment $payment) {
    $trail->parent('admin.order.payment.index');
    $trail->push('Платеж за заказ ' . $payment->order->htmlDate() . ' ' . $payment->order->htmlNum(), route('admin.order.payment.show', $payment));
});

//REFUND
Breadcrumbs::for('admin.order.refund.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Возвраты по заказам', route('admin.order.refund.index'));
});
Breadcrumbs::for('admin.order.refund.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.order.refund.index');
    $trail->push('Создать возврат по заказу', route('admin.order.refund.create'));
});
Breadcrumbs::for('admin.order.refund.show', function (BreadcrumbTrail $trail, OrderRefund $refund) {
    $trail->parent('admin.order.refund.index');
    $trail->push('Возврат по заказу ' . $refund->order->htmlNum() . ' от ' . $refund->order->htmlDate(), route('admin.order.refund.show', $refund));
});
