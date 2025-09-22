<?php
declare(strict_types=1);

namespace App\Modules\Service\Report;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use Illuminate\Contracts\Support\Arrayable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use App\Modules\Base\Service\ReportService;

class InvoiceReport
{
    private string $template;
    private ReportService $service;

    private const int BEGIN_ROW = 15;

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
        if (is_null($order->shopper_id)) {
            $client = $order->userFullName();
        } else {
            $organization = $order->shopper;
            $client = $organization->full_name . ', ИНН ' .
                $organization->inn . ', КПП ' . $organization->kpp . ', ' .
                $organization->legal_address->address() . ', ' .
                phone($organization->phone);
        }
        //Данные о продавце
        $trader = $order->trader->full_name . ', ИНН ' .
            $order->trader->inn . ', КПП ' . $order->trader->kpp . ', ' .
            $order->trader->legal_address->address() . ', ' .
            phone($order->trader->phone);

        $vat_order = $order->getVAT();

        //...
        $replaceItems = [
            '{num-date}' => $order->htmlNumDate(),
            '{client}' => $client,
            '{trader}' => $trader,
            '{quantity}' => (string)($order->getQuantity() + $order->additions()->count()),
            '{amount}' => price($order->getTotalAmount()),
            '{amount_text}' => $this->service->PriceToText($order->getTotalAmount()),
            '{bank}' => $order->trader->bank_name,
            '{bik}' => $order->trader->bik,
            '{inn}' => $order->trader->inn,
            '{kpp}' => $order->trader->kpp,

            '{full_name}' => $order->trader->full_name,
            '{corr_account}' => $order->trader->corr_account,
            '{pay_account}' => $order->trader->pay_account,
            '{chief}' => $order->trader->chief->getShortname(),
            '{vat_caption}' => $vat_order == 0 ? 'Без налога (НДС)' : 'В том числе НДС ' . '(' . $order->getVATTrader() . '%)',
            '{vat_value}' => $vat_order == 0 ? '-' : $vat_order,
        ];

        $this->service->findReplaceArray($activeWorksheet, $replaceItems);

        //Список товара
        $_from = self::BEGIN_ROW;
        $_to = self::BEGIN_ROW + 1;
        $count_items = $order->items()->count();
        $count_additions = $order->additions()->count();

        $this->OrderItemsToSheet($order, $activeWorksheet, $_from, $_to, false);
        $this->OrderItemsToSheet($order, $activeWorksheet, $_from, $_to, true);

      /*
        foreach ($order->items()->where('preorder', false)->get() as $i => $item) {

            if ($count_additions != 0 || $i != $count_items - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRowsRange($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $this->ItemToSheet($activeWorksheet, $item, $begin_row_products, $i);
            $_from++;
            $_to++;
        }

        foreach ($order->items()->where('preorder', true)->get() as $i => $item) {

            if ($count_additions != 0 || $i != $count_items - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRowsRange($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $this->ItemToSheet($activeWorksheet, $item, $i);
            $_from++;
            $_to++;
        }
*/
        //Список Услуг
        foreach ($order->additions as $j => $addition) {

            if ($j < $count_additions - 1) {
                $activeWorksheet->insertNewRowBefore($_from, 1);
                $this->service->copyRowsRange($activeWorksheet, 'A' . $_to . ':J' . $_to, 'A' . $_from);
            }

            $activeWorksheet->setCellValue([1, self::BEGIN_ROW + $count_items + $j], ($j + 1 + $count_items));
            $activeWorksheet->setCellValue([2, self::BEGIN_ROW + $count_items + $j], $addition->addition->name);
            $activeWorksheet->setCellValue([6, self::BEGIN_ROW + $count_items + $j], '-');
            $activeWorksheet->setCellValue([7, self::BEGIN_ROW + $count_items + $j], 1);
            $activeWorksheet->setCellValue([8, self::BEGIN_ROW + $count_items + $j], 'услуга');
            $activeWorksheet->setCellValue([9, self::BEGIN_ROW + $count_items + $j], price($addition->amount));
            $activeWorksheet->setCellValue([10, self::BEGIN_ROW + $count_items + $j], price($addition->amount));

            $_from++;
            $_to++;
        }

        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(1);

        return $spreadsheet;
    }

    private function OrderItemsToSheet(Order $order, $activeWorksheet,
                                       int &$from, int &$to,
                                       bool|null $preorder = null): void
    {
        $count_items = $order->items()->count();
        $count_additions = $order->additions()->count();

        if (is_null($preorder)) {
            $items = $order->items()->get();
        } else if($preorder) {
            $items = $order->items()->where('preorder', false)->get();
        } else {
            $items = $order->items()->where('preorder', true)->get();
        }
        foreach ($items as $i => $item) {

            if ($count_additions != 0 || $i != $count_items - 1) {
                $activeWorksheet->insertNewRowBefore($from, 1);
                $this->service->copyRowsRange($activeWorksheet, 'A' . $to . ':J' . $to, 'A' . $from);
            }

            $this->ItemToSheet($activeWorksheet, $item, $i);
            $from++;
            $to++;
        }

    }

    private function ItemToSheet($activeWorksheet, OrderItem $item, $i): void
    {
        $activeWorksheet->setCellValue([1, self::BEGIN_ROW + $i], ($i + 1));
        $activeWorksheet->setCellValue([2, self::BEGIN_ROW + $i], $item->product->name);
        $activeWorksheet->setCellValue([6, self::BEGIN_ROW + $i], $item->product->code);
        $activeWorksheet->setCellValue([7, self::BEGIN_ROW + $i], $item->quantity);
        $activeWorksheet->setCellValue([8, self::BEGIN_ROW + $i], $item->product->measuring->name);
        $activeWorksheet->setCellValue([9, self::BEGIN_ROW + $i], price($item->sell_cost));
        $activeWorksheet->setCellValue([10, self::BEGIN_ROW + $i], price($item->sell_cost * $item->quantity));
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
