<?php
declare(strict_types=1);

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderPayment;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
