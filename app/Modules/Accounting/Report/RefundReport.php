<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Base\Service\ReportParams;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RefundReport extends AccountingReport
{
    protected string $class = 'refund';
    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Возврат поставщику (xls)',
            'reportPDF' => 'Возврат поставщику (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $refund = RefundDocument::find($document_id);
        $params = ReportParams::utd();
        $params->notInterim();
        $params->notAmount();
        //Заполняем общие статичные данные

        $distributor = $refund->arrival->supply->distributor;
        $distributor_org = $refund->arrival->supply->organization;
        $customer = $refund->arrival->supply->customer;

        $amount_total = $refund->getAmount();
        $amount_vat = $refund->getAmountVAT();
        $amount = $amount_total - $amount_vat;

        $replaceItems = [
            '{number}' => $refund->number,
            '{date}' => $refund->created_at->translatedFormat('d.m.Y'),
            //TODO ****
            '{trader.name}' => $customer->full_name,
            '{trader.short_name}' => $customer->short_name,
            '{trader.address}' => $customer->actual_address->address(true),
            '{trader.inn/kpp}' => $customer->inn . '/' . $customer->kpp,

            '{shopper.name}' => $distributor_org->full_name,
            '{shopper.short_name}' => $distributor_org->short_name,
            '{shopper.address}' => $distributor_org->actual_address->address(true),
            '{shopper.inn/kpp}' => $distributor_org->inn . '/' . $customer->kpp,
            '{shopper.currency}' => ($distributor->foreign) ? ($distributor->currency->name . ', ' . $distributor->currency->code) : '',

            '{status}' => '2',
            '{chief}' => $customer->chief->getShortname(),
            '{post}' => $customer->post,
            '{date.d}' => $refund->created_at->translatedFormat('d'),
            '{date.m}' => $refund->created_at->translatedFormat('M'),
            '{date.y}' => $refund->created_at->translatedFormat('Y'),

            '{amount}' => $amount,
            '{amount.vat}' => $amount_vat,
            '{amount.total}' => $amount_total,

        ];

        return $this->SpreadSheet('utd', $replaceItems, $params, $refund->products()->getModels()); //$refund->products()->getModels()

    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var RefundProduct $item */
        $name = empty($item->product->name_print) ? $item->product->name : $item->product->name_print;

        $vat = !is_null($item->product->VAT->value) ? ceil($item->product->VAT->value * $item->cost_currency / 100) : 0;
        $not_vat = $item->cost_currency - $vat;
        $activeWorksheet->setCellValue([1, $row], ($position + 1));
        $activeWorksheet->setCellValue([2, $row], $item->product->code);
        $activeWorksheet->setCellValue([5, $row], $name);
        $activeWorksheet->setCellValue([13, $row], $item->product->measuring->name);
        $activeWorksheet->setCellValue([16, $row], $item->quantity);
        $activeWorksheet->setCellValue([19, $row], $not_vat);
        $activeWorksheet->setCellValue([22, $row], $item->cost_currency * $item->quantity);
        $activeWorksheet->setCellValue([26, $row], $item->product->VAT->name);
        $activeWorksheet->setCellValue([27, $row], !is_null($item->product->VAT->value) ? $vat : '-');
        $activeWorksheet->setCellValue([29, $row], $item->cost_currency);

    }

    public function emptyAmount(): array
    {
        return [];
    }

    public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void
    {
        // TODO: Implement rowInterim() method.
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        // TODO: Implement rowAmount() method.
    }
}
