<?php
declare(strict_types=1);

namespace App\Modules\Service\Report;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use JetBrains\PhpStorm\ArrayShape;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Modules\Base\Service\ReportService;

class Trade12Report
{
    private string $template;
    private ReportService $service;

    const FIRST_START = 10;
    const FIRST_FINISH = 20;
    const NEXT_START = 18;
    const NEXT_FINISH = 40;
    const BEGIN_ROW = 23; //Первая позиция в списке

    private Worksheet $activeWorksheet;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
        $this->template = $this->service->template('trade12');
    }

    public function xlsx(OrderExpense $expense): string
    {
        $spreadsheet = $this->create_xls($expense);

        $file = storage_path() . '/report/expense/' . $expense->id . '.xlsx';

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        return $file;
    }

    private function create_xls(OrderExpense $expense): Spreadsheet
    {
        $spreadsheet = IOFactory::load($this->template);
        $this->activeWorksheet = $spreadsheet->getActiveSheet();

        $count_items = $expense->items()->count();
        $count_additions = $expense->additions()->count();
        $pages = $this->getList($count_items + $count_additions); //Разбивка на страницы

        $this->_general_info($expense, count($pages)); //Заполняем основные данные об организации и общие по счету

        $list_items = array_merge(
            $expense->items()->getModels(),
            $expense->additions()->getModels()
        );
        $start = 0;
        $amount = $this->emptyAmount();
        foreach ($pages as $n => $page) { //Разбиваем на страницы
            $amount_page = $this->emptyAmount(); //Обнуляем данные Итого по странице
            for ($i = $start; $i < $start + $page; $i++) {
                if ($i != count($list_items) - 1) $this->_row_insert($i + $n * 4);

                $this->_row($i + $n * 4, $list_items[$i], $amount_page);
            }
            //Суммируем итого по страницам
            $amount['quantity'] += $amount_page['quantity'];
            $amount['weight'] += $amount_page['weight'];
            $amount['amount'] += $amount_page['amount'];
            $amount['amount_nds'] += $amount_page['amount_nds'];

            if ($n != count($pages) - 1) {
                $this->_divide_insert($i, 'Страница ' . ($n + 2));
                $this->_row_amount($i, $amount_page);
                $start += $page;
            } else { //Для последней строки без вставки разделения
                $this->_row_amount($i + $n * 4, $amount_page);
                $this->_row_amount($i + $n * 4 + 1, $amount);//Добавляем ВСЕГО
            }
        }
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);

        return $spreadsheet;
    }

    private function _general_info(OrderExpense $expense, int $page_count)
    {

        //TODO Выбор организации продавца, default или по id
        /** @var Trader $trader */
        $trader = Trader::where('default', true)->first();
        $organization = $trader->organization;
        $organization_text = $organization->full_name .
            ', ИНН ' . $organization->inn .
            ', ' . $organization->legal_address->post . ', ' . $organization->legal_address->address .
            ', тел: ' . $organization->phone .
            ', р/с ' . $organization->pay_account . ', в банке ' . $organization->bank_name . ', БИК ' . $organization->bik . ', к/с ' . $organization->corr_account;
        $_count = $expense->items()->count() + $expense->additions()->count();
        $_quantity = $expense->getQuantity();

        for ($row = 1; $row < 50; $row++) {
            for ($col = 1; $col < 50; ++$col) {
                $value = $this->activeWorksheet->getCell([$col, $row])->getValue();
                if (!is_null($value)) {

                    $value = str_replace('{organization}', (string)$organization_text, (string)$value);
                    $value = str_replace('{storage}', (string)$expense->storage->name, (string)$value);
                    $value = str_replace('{user}', (string)$expense->order->userFullName(), (string)$value);
                    $value = str_replace('{document}', (string)$expense->order->user->getDocumentName(), (string)$value);
                    $value = str_replace('{number}', (string)$expense->number, (string)$value);
                    $value = str_replace('{date}', (string)$expense->created_at->format('d.m.Y'), (string)$value);

                    $value = str_replace('{day}', (string)$expense->created_at->format('d'), (string)$value);
                    $value = str_replace('{month}', (string)$expense->created_at->translatedFormat('F'), (string)$value);
                    $value = str_replace('{year}', (string)$expense->created_at->format('Y'), (string)$value);

                    $value = str_replace('{post_chief}', (string)$organization->post, (string)$value);
                    $value = str_replace('{chief}', (string)$organization->chief->getShortname(), (string)$value);

                    $value = str_replace('{count}', $this->service->CountToText($_count), (string)$value);
                    $value = str_replace('{quantity}', $this->service->CountToText($_quantity, true), (string)$value);
                    $value = str_replace('{amount_text}', $this->service->PriceToText($expense->getAmount()), (string)$value);//Сумма по заказу

                    $value = str_replace('{pages}', (string)$page_count, (string)$value);//Сумма по заказу

                    $this->activeWorksheet->setCellValue([$col, $row], $value);
                }
            }
        }
    }

    private function _row_insert(int $i)
    {
        $row = self::BEGIN_ROW + $i;
        $this->activeWorksheet->insertNewRowBefore($row, 1);
        $this->service->copyRowsRange($this->activeWorksheet, 'A' . ($row + 1) . ':AO' . ($row + 1), 'A' . $row);
    }

    private function _divide_insert(int $i, string $name_page)
    {
        $row = self::BEGIN_ROW + $i;
        //Итого на страницу
        $this->activeWorksheet->insertNewRowBefore($row, 2);
        $this->service->copyRowsRange($this->activeWorksheet, 'A' . ($row + 3) . ':AN' . ($row + 3), 'A' . $row);
        //Разрыв печати
        $this->activeWorksheet->setBreak('A' . $row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //Разделитель
        $this->service->copyRowsRange($this->activeWorksheet, 'A19:AO19', 'A' . $row + 1);
        $this->activeWorksheet->setCellValue([40, self::BEGIN_ROW + $i + 1], $name_page);
        //Шапка
        $row += 2;
        $this->activeWorksheet->insertNewRowBefore($row, 2);
        $this->service->copyRowsRange($this->activeWorksheet, 'A20:AO21', 'A' . $row);
    }

    private function _row(int $i, OrderExpenseItem|OrderExpenseAddition $element, array &$amount)
    {
        if ($element instanceof OrderExpenseItem) $this->_row_item($i, $element, $amount);
        if ($element instanceof OrderExpenseAddition) $this->_row_addition($i, $element, $amount);
    }

    private function _row_item(int $i, OrderExpenseItem $item, array &$amount)
    {
        $this->activeWorksheet->setCellValue([2, self::BEGIN_ROW + $i], ($i + 1));
        $this->activeWorksheet->setCellValue([3, self::BEGIN_ROW + $i], $item->orderItem->product->name);
        $this->activeWorksheet->setCellValue([8, self::BEGIN_ROW + $i], $item->orderItem->product->code);
        $this->activeWorksheet->setCellValue([9, self::BEGIN_ROW + $i], 'шт.');
        $this->activeWorksheet->setCellValue([13, self::BEGIN_ROW+ $i], '796');
        $this->activeWorksheet->setCellValue([14, self::BEGIN_ROW+ $i], 'шт.');
        $this->activeWorksheet->setCellValue([15, self::BEGIN_ROW+ $i], $item->quantity);
        $this->activeWorksheet->setCellValue([18, self::BEGIN_ROW+ $i], $item->quantity);
        $this->activeWorksheet->setCellValue([24, self::BEGIN_ROW+ $i], $item->orderItem->product->weight() * $item->quantity); //Вес

        $this->activeWorksheet->setCellValue([26, self::BEGIN_ROW+ $i], $item->orderItem->sell_cost);
        $this->activeWorksheet->setCellValue([29, self::BEGIN_ROW+ $i], $item->orderItem->sell_cost * $item->quantity);
        $this->activeWorksheet->setCellValue([39, self::BEGIN_ROW+ $i], $item->orderItem->sell_cost * $item->quantity);

        $amount['quantity'] += $item->quantity;
        $amount['weight'] += $item->orderItem->product->weight() * $item->quantity;
        $amount['amount'] += $item->orderItem->sell_cost * $item->quantity;
        $amount['amount_nds'] = $amount['amount'];
    }

    private function _row_addition(int $i, OrderExpenseAddition $addition, array &$amount)
    {
        $this->activeWorksheet->setCellValue([2, self::BEGIN_ROW+ $i], ($i + 1));
        $this->activeWorksheet->setCellValue([3, self::BEGIN_ROW+ $i], $addition->orderAddition->addition->name . ' (' . $addition->orderAddition->comment . ')');
        $this->activeWorksheet->setCellValue([8, self::BEGIN_ROW+ $i], '');
        $this->activeWorksheet->setCellValue([9, self::BEGIN_ROW+ $i], 'услуга');
        $this->activeWorksheet->setCellValue([13, self::BEGIN_ROW+ $i], '356');
        $this->activeWorksheet->setCellValue([14, self::BEGIN_ROW+ $i], 'услуга');
        $this->activeWorksheet->setCellValue([15, self::BEGIN_ROW+ $i], 1);
        $this->activeWorksheet->setCellValue([18, self::BEGIN_ROW+ $i], 1);

        $this->activeWorksheet->setCellValue([26, self::BEGIN_ROW+ $i], $addition->amount);
        $this->activeWorksheet->setCellValue([29, self::BEGIN_ROW+ $i], $addition->amount);
        $this->activeWorksheet->setCellValue([39, self::BEGIN_ROW+ $i], $addition->amount);

        $amount['quantity']++;
        $amount['amount'] += $addition->amount;
        $amount['amount_nds'] = $amount['amount'];
    }

    private function _row_amount(int $i, array $data)
    {
        $this->activeWorksheet->setCellValue([18, self::BEGIN_ROW+ $i], $data['quantity']);
        $this->activeWorksheet->setCellValue([24, self::BEGIN_ROW+ $i], $data['weight']);
        $this->activeWorksheet->setCellValue([29, self::BEGIN_ROW+ $i], $data['amount']);
        $this->activeWorksheet->setCellValue([39, self::BEGIN_ROW+ $i], $data['amount_nds']);
    }

    private function getList(int $count): array
    {
        $result = [];
        while ($count > 0) {
            if (count($result) == 0) { //Первый лист
                if ($count > self::FIRST_FINISH) {
                    $result[] = self::FIRST_FINISH;
                    $count -= self::FIRST_FINISH;
                } elseif ($count > self::FIRST_START) {
                    $result[] = self::FIRST_START;
                    $count -= self::FIRST_START;
                } else {
                    $result[] = $count;
                    $count = 0;
                }
            } else { //Последующие листы
                if ($count > self::NEXT_FINISH) {
                    $result[] = self::NEXT_FINISH;
                    $count -= self::NEXT_FINISH;
                }
                if ($count > self::NEXT_START && $count <= self::NEXT_FINISH) {
                    $result[] = self::NEXT_START;
                    $count -= self::NEXT_START;
                }
                if ($count <= self::NEXT_START) {
                    $result[] = $count;
                    $count = 0;
                }
            }
        }

        return $result;
    }

    #[ArrayShape(['quantity' => "int", 'weight' => "int", 'amount' => "int", 'amount_nds' => "int"])]
    private function emptyAmount(): array
    {
        return [
            'quantity' => 0,
            'weight' => 0,
            'amount' => 0,
            'amount_nds' => 0,
        ];
    }
}
