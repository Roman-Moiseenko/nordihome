<?php
declare(strict_types=1);

namespace App\Modules\Service\Report;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use App\Modules\Base\Service\ReportService;

class InvoiceReport
{
    private string $template;
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
        $this->template = $this->service->template('invoice');
    }

    private function create_xls(Order $order): Spreadsheet
    {
        $spreadsheet = IOFactory::load($this->template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

        //Данные о клиенте
        if (is_null($order->user->organization)) {
            $client = $order->userFullName();
        } else {
            $organization = $order->user->organization;
            $client = $organization->full_name . ', ИНН ' .
                $organization->inn . ', КПП ' . $organization->kpp . ', ' .
                $organization->legal_address->address() . ', ' .
                phone($organization->phone);
        }
        //Данные о продавце
        $trader = $order->organization->full_name . ', ИНН ' .
            $order->organization->inn . ', КПП ' . $order->organization->kpp . ', ' .
            $order->organization->legal_address->address() . ', ' .
            phone($order->organization->phone);
        //...
        $replaceItems = [
            '{num-date}' => $order->htmlNumDate(),
            '{client}' => $client,
            '{trader}' => $trader,
            '{quantity}' => (string)($order->getQuantity() + $order->additions()->count()),
            '{amount}' => price($order->getTotalAmount()),
            '{amount_text}' => $this->service->PriceToText($order->getTotalAmount()),
            '{bank}' => $order->organization->bank_name,
            '{bik}' => $order->organization->bik,
            '{inn}' => $order->organization->inn,
            '{kpp}' => $order->organization->kpp,

            '{full_name}' => $order->organization->full_name,
            '{corr_account}' => $order->organization->corr_account,
            '{pay_account}' => $order->organization->pay_account,
            '{chief}' => $order->organization->chief->getShortname(),

        ];
        $this->service->findReplaceArray($activeWorksheet, $replaceItems);

        //Список товара
        $begin_row_products = 15; //TODO Перенести в настройки отчетов
        $_from = $begin_row_products;
        $_to = $begin_row_products + 1;
        $count_items = $order->items()->count();
        $count_additions = $order->additions()->count();

        foreach ($order->items as $i => $item) {

            if ($count_additions != 0 || $i != $count_items - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRowsRange($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
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
                $this->service->copyRowsRange($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $activeWorksheet->setCellValue([1, $begin_row_products + $count_items + $j], ($j + 1 + $count_items));
            $activeWorksheet->setCellValue([2, $begin_row_products + $count_items + $j], $addition->addition->name);
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
