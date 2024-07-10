<?php

namespace App\Livewire\Admin\Accounting;

use App\Forms\ModalDelete;
use App\Helpers\IconHelper;
use App\Modules\Admin\Entity\Admin;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Modules\Accounting\Entity\PricingDocument;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class PricingTable extends DataTableComponent
{
    //protected $model = PricingDocument::class;

    public bool $sortingPillsStatus = false;

    public function builder(): Builder
    {
        return PricingDocument::query()->with(['arrival:id,created_at,number', 'staff:fullname,id']);
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
                ];})->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) {
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
                return route('admin.accounting.pricing.show', $row->id);
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
            Column::make("Id", "id")->hideIf(true),
            Column::make("Дата", "created_at")
                ->sortable()
                ->format(fn($value, $row, Column $column) => $row->htmlDate())
                ->html(),
            Column::make("№ документа", "number")
                ->sortable()->format(fn($value, $row, Column $column) => $row->htmlNum()),
            BooleanColumn::make("Проведен", "completed")->sortable(),
            Column::make("Ответственный", "staff_id")
                ->sortable()->format(function($value, $row, Column $column) {
                    if (!is_null($row->staff)) return $row->staff->fullname->getFullName();
                    return '-';
                }),
            Column::make("Комментарий", "comment"),
            Column::make("Поступление", "arrival_id")->format(function($value, $row, Column $column) {
                if (!is_null($row->arrival)) return $row->arrival->htmlNumDate();
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
                        if (!$row->isCompleted()) {
                            return ModalDelete::attributes(route('admin.accounting.pricing.destroy', $row));
                        } else {
                            return ['style' => 'display: none',];
                        }
                    }),

            ]),

        ];
    }

    public function filters(): array
    {
        $admins[0] = '';
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
            SelectFilter::make('Статус', 'completed')
                ->options([
                    '' => 'Все',
                    '0' => 'Черновики',
                    '1' => 'Проведенные',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value == '0') $builder->where('completed', false);
                    if ($value == '1') $builder->where('completed', true);

                }),
            TextFilter::make('№ Документа')
                ->config([
                    // 'placeholder' => 'Search Name',
                    'maxlength' => '5',
                ])
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('number', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('Ответственный', 'staff_id')
                ->options($admins)
                ->filter(function(Builder $builder, string $value) {
                    if ((int)$value != 0) {
                        $builder->where('staff_id', (int)$value);
                    }
                }),
        ];
    }
}
