<?php

namespace App\Livewire\Admin\Sales;

use App\Forms\ModalDelete;
use App\Helpers\IconHelper;
use App\Modules\Admin\Entity\Admin;
use App\Modules\User\Entity\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Modules\Order\Entity\Order\OrderPayment;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class PaymentTable extends DataTableComponent
{
    //protected $model = OrderPayment::class;

    public bool $sortingPillsStatus = false;

    public function builder(): Builder
    {
        return OrderPayment::query()->with(['staff:fullname,id', 'order:id,user_id,created_at,number', 'order.user:id,fullname']);
    }

    /**
     * @throws \Rappasoft\LaravelLivewireTables\Exceptions\DataTableConfigurationException
     */
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
                ];})->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) {
                if ($column->isField('document') || $column->isField('staff_id') || $column->isField('user_id')) {
                    return [
                        'default' => false,
                        'class' => 'px-6 py-4 text-sm dark:text-white',
                    ];
                }
                return ['default' => true];
            })
            ->setColumnSelectDisabled()
            ->setTableRowUrl(function($row) {
                return route('admin.sales.payment.edit', $row->id);
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
            Column::make("Дата", "created_at")
                ->sortable()
                ->format(fn($value, $row, Column $column) => $row->htmlDate())
                ->html(),
            Column::make("Сумма", "amount")
                ->sortable()->format(fn($value, $row, Column $column) => price($row->amount)),
            Column::make('Заказ', 'order_id')
                ->format(fn($value, $row, Column $column) => $row->order->htmlNumDate()),
            Column::make('Клиент', 'order.user_id')->sortable()
                ->format(fn($value, $row, Column $column) => $row->order->user->fullname->getFullName()),
            Column::make("Документ", "document"),
            Column::make("Ответственный", "staff_id")
                ->sortable()->format(function($value, $row, Column $column) {
                    if (!is_null($row->staff)) return $row->staff->fullname->getFullName();
                    return '-';
                }),
            ButtonGroupColumn::make('Действия')->unclickable()->attributes(function($row) {
                return [
                    'class' => 'space-x-2',
                ];
            })->buttons([
                LinkColumn::make('Delete') // make() has no effect in this case but needs to be set anyway
                ->title(fn($row) => IconHelper::trash() . ' Delete')->html()
                    ->location(fn($row) => '#')
                    ->attributes(function($row) {
                        return ModalDelete::attributes(route('admin.sales.payment.destroy', $row));
                    }),

            ])
        ];
    }

    public function filters(): array
    {
        $admins[0] = 'Выберите ответственного';
        foreach (Admin::getModels() as $admin) {
            $admins[$admin->id] = $admin->fullname->getShortName();
        }

        return [
            DateFilter::make('Период c')->filter(function(Builder $builder, string $value) {
                $builder->where('created_at', '>=', $value);
            }),
            DateFilter::make('Период по')->filter(function(Builder $builder, string $value) {
                $builder->where('created_at', '<=', $value);
            }),
            TextFilter::make('', 'order_number')
                ->config([
                    'placeholder' => '№ Заказа',
                    'maxlength' => '5',
                ])
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereHas('order', function ($q) use($value) {
                        $q->where('number', (int)$value);
                    });
                }),
            TextFilter::make('', 'user')
                ->config([
                    'placeholder' => 'Клиент,Телефон,ИНН,Email',
                ])
                ->filter(function(Builder $builder, string $value) {
                    $users = User::where('phone', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhereRaw("LOWER(fullname) like LOWER('%$value%')")
                        ->pluck('id')->toArray();

                   $builder->whereHas('order', function ($q) use ($users) {
                       $q->whereIn('user_id', $users);
                   });
                }),


            SelectFilter::make('', 'staff_id')
                ->options($admins)
                ->filter(function(Builder $builder, string $value) {
                    if ((int)$value != 0) {
                        $builder->where('staff_id', (int)$value);
                    }
                }),
        ];
    }
}
