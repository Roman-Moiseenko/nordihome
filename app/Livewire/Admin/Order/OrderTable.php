<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Order\Helpers\OrderHelper;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Modules\Order\Entity\Order\Order;

class OrderTable extends DataTableComponent
{
    //protected $model = Order::class;
    public bool $sortingPillsStatus = false;

    public function builder(): Builder
    {
        return Order::query()->with(['items', 'user:fullname, phone', 'statuses', 'manager:fullname']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setTableWrapperAttributes([
                'default' => false,
                'class' => 'box p-4',])
            ->setTableAttributes([
                'default' => false,
                'class' => 'w-full text-left table table-hover',])
            ->setTheadAttributes([
                'default' => false,
                'class' => 'table-dark',])
            ->setThAttributes(function(Column $column) {
                return [
                    'default' => true,
                    'class' => 'text-gray-50',
                ];
            })
            ->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) {
                    //****
                if ($column->isField('comment') || $column->isField('staff_id')) {
                    return [
                        'default' => false,
                        'class' => 'px-6 py-4 text-sm dark:text-white',
                    ];
                }
                return ['default' => true];
            })
            ->setColumnSelectDisabled()
            ->setTableRowUrl(function($row) {
                return route('admin.order.show', $row->id);
            })
            ->setPerPageAccepted([20, 50, 100])
            ->setSearchDisabled()
            ->setFiltersEnabled()
            ->setFilterLayoutPopover()
            ->setFilterPillsStatus(false);
    }

    public function columns(): array
    {
        return [
            Column::make('id', 'id')->hideIf(true),
            Column::make("ОПЛ", 'id')
                ->format(fn($value, $row, Column $column) =>
                OrderHelper::pictogram($row))
                ->html(),
            Column::make("ОТГ", 'id')
                ->format(fn($value, $row, Column $column) =>
                '*')
                ->html(),
            Column::make("№", "number")
                ->format(fn($value, $row, Column $column) => $row->htmlNum())
                ->sortable(),
            Column::make("Дата", "created_at")
                ->format(fn($value, $row, Column $column) => $row->htmlDate())
                ->sortable(),
            Column::make("Ответственный", "manager.fullname")
                ->format(function($value, $row, Column $column) {
                    $fullname = json_decode($value, true);
                   // return price(5);
                    return '***';

                })
                ->sortable(),




            Column::make("Дата", "user.fullname")
                ->sortable(),
            Column::make("Paid", "paid")->hideIf(true),
            Column::make("Finished", "finished")->hideIf(true),
            Column::make("Discount amount", "discount_amount")->hideIf(true),
            Column::make("Manual", "manual")->hideIf(true),
            Column::make("Comment", "comment")
                ->sortable(),

        ];
    }
}
