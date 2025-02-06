<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasRefund;
use App\Modules\Accounting\Service\BatchSaleService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefundAddition;
use App\Modules\Order\Entity\Order\OrderExpenseRefundItem;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;

class RefundService
{

    private LoggerService $logger;
    private BatchSaleService $batchSaleService;

    public function __construct(LoggerService $logger, BatchSaleService $batchSaleService)
    {
        $this->logger = $logger;
        $this->batchSaleService = $batchSaleService;
    }

    public function create(OrderExpense $expense, $request): OrderExpenseRefund
    {
        DB::transaction(function () use ($expense, $request, &$refund) {
            /** @var Admin $staff */
            $staff = Auth::guard('admin')->user();

            $refund = OrderExpenseRefund::register($expense->id, $staff->id, $request->input('reason'));

            foreach ($expense->items as $item) {
                $refund->items()->save(OrderExpenseRefundItem::new($item->id, $item->quantityNotRefund()));
            }

            foreach ($expense->additions as $addition) {
                $refund->additions()->save(OrderExpenseRefundAddition::new($addition->id, $addition->amountNotRefund()));
            }

            $order = $refund->expense->order;
            $this->logger->logOrder($order, 'Создан документ на возврат', '', '',
                route('admin.order.refund.show', $refund)
            );
        });

        return $refund;
    }

    public function completed(OrderExpenseRefund $refund)
    {
        //TODO
        foreach ($refund->items as $item) {
            if ($item->expenseItem->quantityNotRefund() < $item->quantity) throw new \DomainException('Кол-во превышает выданного');
        }
        foreach ($refund->additions as $addition) {
            if ($addition->expenseAddition->amountNotRefund() < $addition->amount) throw new \DomainException('Сумма превышает выданного');
        }
        $refund->completed = true;
        $refund->number = (int)OrderExpenseRefund::where('number', '<>', null)->max('number') + 1;
        $refund->save();

        $this->batchSaleService->returnByRefund($refund);
    }

    public function work(OrderExpenseRefund $refund)
    {
        //TODO ????
        $refund->completed = false;
        $refund->save();

    }

    public function throw(OrderExpenseRefund $refund): void
    {
        $refund->items()->delete();
        $refund->additions()->delete();
        $refund->save();
        $expense = $refund->expense;
        foreach ($expense->items as $item) {
            $refund->items()->save(OrderExpenseRefundItem::new($item->id, $item->quantityNotRefund()));
        }
        foreach ($expense->additions as $addition) {
            $refund->additions()->save(OrderExpenseRefundAddition::new($addition->id, $addition->amountNotRefund()));
        }
    }

    public function setInfo(OrderExpenseRefund $refund, Request $request): void
    {
        $refund->reason = $request->input('reason');
        $refund->comment = $request->string('comment')->trim()->value();
        $refund->save();
    }

    public function setItem(OrderExpenseRefundItem $item, Request $request): void
    {
        $quantity = $request->integer('quantity');
        if ($item->expenseItem->quantityNotRefund() < $quantity) throw new \DomainException('Кол-во превышает выданного');
        $item->quantity = $quantity;
        $item->save();
    }

    public function delItem(OrderExpenseRefundItem $item): void
    {
        $item->delete();
    }

    public function setAddition(OrderExpenseRefundAddition $addition, Request $request): void
    {
        $amount = $request->integer('amount');
        if ($addition->expenseAddition->amountNotRefund() < $amount) throw new \DomainException('Сумма превышает выданного');
        $addition->amount = $amount;
        $addition->save();
    }

    public function delAddition(OrderExpenseRefundAddition $addition): void
    {
        $addition->delete();
    }

}
