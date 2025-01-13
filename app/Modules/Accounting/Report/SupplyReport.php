<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Base\Service\ReportParams;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplyReport extends AccountingReport
{
    protected string $class = 'supply';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Заказ поставщику (xls)',
            'reportPDF' => 'Заказ поставщику (pdf)',
        //    'reportPDFSend' => 'Отправить поставщику (pdf)',
        ];
        return $this->renderArray($items);
    }

    public function reportPDFSend(int $supply_id)
    {
        //TODO Отправка системных писем
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $supply = SupplyDocument::find($document_id);
        $params = new ReportParams(11, 28, 35, 33, 40,
            2, 8, 9, 10,
            'Заказ поставщику № ' . $supply->number . ' от ' . $supply->created_at->translatedFormat('d.m.Y')
        );

        //Заполняем общие статичные данные
        $amount_total = $supply->getAmount();
        $amount_vat = $supply->getAmountVAT();

        $replaceItems = [
            '{number}' => $supply->number,
            '{date}' => $supply->created_at->translatedFormat('d.m.Y'),
            '{amount}' => $amount_total - $amount_vat,
            '{amount_vat}' => $amount_vat,
            '{amount_total}' => $amount_total,
            '{amount_text}' => $this->service->PriceToText($amount_total, $supply->currency->sign),
            '{staff}' => $supply->staff->fullname->getShortname(),
            '{trader}' => $this->service->OrganizationText($supply->supply->customer, false),
            '{distributor}' => $supply->organization->short_name,
            '{currency}' => $supply->currency->cbr_code,
            '{count}' => $supply->products()->count(),
        ];

        return $this->SpreadSheet('supply', $replaceItems, $params, $supply->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var SupplyProduct $item */
        $amount = $item->quantity * $item->cost_currency;
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->code);
        $activeWorksheet->setCellValue([4, $row], $item->product->name);
        $activeWorksheet->setCellValue([5, $row], $item->quantity);
        $activeWorksheet->setCellValue([6, $row], $item->product->measuring->name);
        $activeWorksheet->setCellValue([7, $row], $item->cost_currency);
        $activeWorksheet->setCellValue([8, $row], $amount);

        $amount_page['quantity'] += $item->quantity;
        $amount_page['amount'] += $amount;
    }

    public function emptyAmount(): array
    {
        return [
            'quantity' => 0,
            'amount' => 0,
        ];
    }

    public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void
    {
        $activeWorksheet->mergeCells([6, $row, 7, $row]);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], 'На странице');
        $activeWorksheet->setCellValue([5, $row], $amount_page['quantity']);
        $activeWorksheet->setCellValue([8, $row], $amount_page['amount']);
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        $activeWorksheet->mergeCells([6, $row, 7, $row]);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], 'Всего');
        $activeWorksheet->setCellValue([5, $row], $amount_document['quantity']);
        $activeWorksheet->setCellValue([8, $row], $amount_document['amount']);
    }
}
