<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Base\Service\ReportParams;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArrivalReport extends AccountingReport
{
    protected string $class = 'arrival';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Приходная накладная (xls)',
            'reportPDF' => 'Приходная накладная (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $arrival = ArrivalDocument::find($document_id);
        $params = new ReportParams(11, 28, 35, 33, 40,
            2, 8, 9, 10,
            'Приходная накладная № ' . $arrival->number . ' от ' . $arrival->created_at->translatedFormat('d.m.Y')
        );

        //Заполняем общие статичные данные
        $amount_total = $arrival->getAmount();
        $amount_vat = $arrival->getAmountVAT();
        $trader = [
            $arrival->supply->customer->full_name,
            'ИНН ' . $arrival->supply->customer->inn,
            'КПП ' . $arrival->supply->customer->kpp,
            $arrival->supply->customer->legal_address->post . ', ' . $arrival->supply->customer->legal_address->address,
            phone($arrival->supply->customer->phone)
        ];

        $replaceItems = [
            '{number}' => $arrival->number,
            '{date}' => $arrival->created_at->translatedFormat('d.m.Y'),
            '{amount}' => $amount_total - $amount_vat,
            '{amount_vat}' => $amount_vat,
            '{amount_total}' => $amount_total,
            '{amount_text}' => $this->service->PriceToText($amount_total, $arrival->currency->sign),
            '{staff}' => $arrival->staff->fullname->getShortname(),
            '{trader}' => implode(', ', $trader),
            '{distributor}' => $arrival->supply->organization->short_name,
            '{currency}' => $arrival->currency->cbr_code,
            '{count}' => $arrival->products()->count(),
        ];
        return $this->SpreadSheet('arrival', $replaceItems, $params, $arrival->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var ArrivalProduct $item */
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
        $this->_row($activeWorksheet, $row, $amount_page, 'На странице');
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        $this->_row($activeWorksheet, $row, $amount_document, 'Всего');
    }

    private function _row(Worksheet &$activeWorksheet, int $row, array $amount, string $caption)
    {
        $activeWorksheet->mergeCells([6, $row, 7, $row]);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], $caption);
        $activeWorksheet->setCellValue([5, $row], $amount['quantity']);
        $activeWorksheet->setCellValue([8, $row], $amount['amount']);
    }
}
