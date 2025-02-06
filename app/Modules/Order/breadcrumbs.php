<?php
declare(strict_types=1);

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Заказы', route('admin.order.index'));
});


Breadcrumbs::for('admin.order.reserve.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
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
    $trail->push('История заказа', route('admin.order.log', $order));
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
    $text = is_null($payment->order) ? 'Нераспределенный платеж' : 'Платеж за заказ ' . $payment->order->htmlDate() . ' ' . $payment->order->htmlNum();
    $trail->push($text, route('admin.order.payment.show', $payment));
});

//REFUND
Breadcrumbs::for('admin.order.refund.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Возвраты товаров', route('admin.order.refund.index'));
});

Breadcrumbs::for('admin.order.refund.show', function (BreadcrumbTrail $trail, OrderExpenseRefund $refund) {
    $trail->parent('admin.order.refund.index');
    $trail->push('Возврат по заказу ' . $refund->expense->order->htmlNum() . ' от ' . $refund->expense->order->htmlDate(), route('admin.order.refund.show', $refund));
});
//PRODUCT
Breadcrumbs::for('admin.order.product.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.order.index');
    $trail->push('Все товары', route('admin.order.product.index'));
});

Breadcrumbs::for('admin.order.product.show', function (BreadcrumbTrail $trail, \App\Modules\Product\Entity\Product $product) {
    $trail->parent('admin.order.product.index');
    $trail->push($product->name, route('admin.order.product.show', $product));
});
