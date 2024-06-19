<?php
declare(strict_types=1);

namespace App\Modules\Service\Report;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class InvoiceReport
{
    private string $template;
    private array $invoice;
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->invoice = (new Options())->report['invoice'];
        $this->template = storage_path() . $this->invoice['template'];
        $this->service = $service;
    }

    private function create_xls(Order $order): Spreadsheet
    {
        //Открываем шаблон
        $spreadsheet = IOFactory::load($this->template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

        //Данные о клиенте и Данные отчета
        for ($row = 1; $row < 50; $row++) {
            for ($col = 1; $col < 50; ++$col) {
                $value = $activeWorksheet->getCell([$col, $row])->getValue();

                if (!is_null($value)) {
                    $value = str_replace('{num-date}', (string)$order->htmlNumDate(), (string)$value);//Номер и дата
                    $value = str_replace('{client}', (string)$order->userFullName(), (string)$value);//Клиент
                    $value = str_replace('{quantity}', (string)($order->getQuantity() + $order->additions()->count()), (string)$value);//Кол-во товаров
                    $value = str_replace('{amount}', price($order->getTotalAmount()), (string)$value);//Сумма по заказу
                    $value = str_replace('{amount_text}', $this->service->PriceToText($order->getTotalAmount()), (string)$value);//Сумма по заказу

                    $activeWorksheet->setCellValue([$col, $row], $value);
                }
            }
        }

        //Список товара
        $begin_row_products = 15; //TODO Перенести в настройки отчетов
        $_from = $begin_row_products;
        $_to = $begin_row_products + 1;
        $count_items = $order->items()->count();
        $count_additions = $order->additions()->count();

        foreach ($order->items as $i => $item) {

            if ($count_additions != 0 || $i != $count_items - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRows($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $activeWorksheet->setCellValue([1, $begin_row_products + $i], ($i + 1));
            $activeWorksheet->setCellValue([2, $begin_row_products + $i], $item->product->name);
            $activeWorksheet->setCellValue([6, $begin_row_products + $i], $item->product->code);
            $activeWorksheet->setCellValue([7, $begin_row_products + $i], $item->quantity);
            $activeWorksheet->setCellValue([8, $begin_row_products + $i], 'штука');
            $activeWorksheet->setCellValue([9, $begin_row_products + $i], price($item->sell_cost));
            $activeWorksheet->setCellValue([10, $begin_row_products + $i], price($item->sell_cost * $item->quantity));

            $_from++;
            $_to++;
        }

        //Список Услуг
        foreach ($order->additions as $j => $addition) {

            if ($j < $count_additions - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRows($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $activeWorksheet->setCellValue([1, $begin_row_products + $count_items + $j], ($j + 1 + $count_items));
            $activeWorksheet->setCellValue([2, $begin_row_products + $count_items + $j], $addition->purposeHTML());
            $activeWorksheet->setCellValue([6, $begin_row_products + $count_items + $j], '-');
            $activeWorksheet->setCellValue([7, $begin_row_products + $count_items + $j], 1);
            $activeWorksheet->setCellValue([8, $begin_row_products + $count_items + $j], 'услуга');
            $activeWorksheet->setCellValue([9, $begin_row_products + $count_items + $j], price($addition->amount));
            $activeWorksheet->setCellValue([10, $begin_row_products + $count_items + $j], price($addition->amount));

            $_from++;
            $_to++;
        }

        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(1);

        return $spreadsheet;
    }

    public function xlsx(Order $order): string
    {
        $spreadsheet = $this->create_xls($order);

        $file = storage_path() . '/report/order/' . $order->id . '.xlsx';

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        return $file;
    }

    public function pdf(Order $order): string
    {
        $spreadsheet = $this->create_xls($order);
        $file = storage_path() . '/report/order/' . $order->id . '.pdf';

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $writer = new Mpdf($spreadsheet);
        $writer->setEditHtmlCallback(function ($html) {
            return preg_replace('~</style>~', 'table.sheet0 {page-break-inside:avoid} </style>', $html);
        });

        $writer->save($file);
        return $file;
    }


}
