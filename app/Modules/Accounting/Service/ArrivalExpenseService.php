<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseItem;
use App\Modules\Admin\Entity\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArrivalExpenseService
{
    public function create(ArrivalDocument $arrival): ArrivalExpenseDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $expense = ArrivalExpenseDocument::register($staff->id);
        $arrival->expense()->save($expense);
        return $expense;
    }


    public function setInfo(ArrivalExpenseDocument $expense, Request $request): void
    {
        $expense->baseSave($request->input('document'));
        $expense->currency = $request->input('currency');
        $expense->save();
    }

    public function addItem(ArrivalExpenseDocument $expense, Request $request): void
    {
        if ($expense->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        $item = ArrivalExpenseItem::new(
            $request->string('name')->value(),
            $request->integer('quantity'),
            $request->float('cost')
        );

        $expense->items()->save($item);
    }

    public function delItem(ArrivalExpenseItem $item): void
    {
        if ($item->expense->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $item->delete();
    }

    public function setItem(ArrivalExpenseItem $item, Request $request): void
    {
        if ($item->expense->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        $item->name = $request->string('name')->value();
        $item->quantity = $request->integer('quantity');
        $item->cost = $request->float('cost');
        $item->save();
    }

    public function completed(ArrivalExpenseDocument $expense): void
    {
        $expense->completed();
    }

}
