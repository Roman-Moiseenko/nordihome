<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SurplusDocument;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Base\Service\ReportParams;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurplusReport extends AccountingReport
{
    protected string $class = 'surplus';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Акт об оприходовании запасов (xls)',
            'reportPDF' => 'Акт об оприходовании запасов (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        /** @var SurplusDocument $surplus */
        $surplus = SurplusDocument::find($document_id);
        $params = new ReportParams(8, 28, 35, 33, 40,
            2, 8, 6, 7,
            'Акт об оприходовании запасов № ' . $surplus->number . ' от ' . $surplus->created_at->translatedFormat('d.m.Y')
        );

        //Заполняем общие статичные данные
        $amount = $surplus->getAmount();

        $replaceItems = [
            '{number}' => $surplus->number,
            '{date}' => $surplus->created_at->translatedFormat('d.m.Y'),
            '{amount}' => $amount,
            '{amount_text}' => $this->service->PriceToText($amount),
            '{staff}' => $surplus->staff->fullname->getShortname(),
            '{storage}' => $surplus->storage->name,
            '{count}' => $surplus->products()->count(),
        ];

        return $this->SpreadSheet('surplus', $replaceItems, $params, $surplus->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var SurplusProduct $item */
        $amount = $item->quantity * $item->cost;
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->code);
        $activeWorksheet->setCellValue([4, $row], $item->product->name);
        $activeWorksheet->setCellValue([5, $row], $item->quantity);
        $activeWorksheet->setCellValue([6, $row], $item->product->measuring->name);
        $activeWorksheet->setCellValue([7, $row], $item->cost);
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
        $activeWorksheet->setCellValue([8, $row], price($amount_page['amount']));
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        $activeWorksheet->mergeCells([6, $row, 7, $row]);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], 'Всего');
        $activeWorksheet->setCellValue([5, $row], $amount_document['quantity']);
        $activeWorksheet->setCellValue([8, $row], price($amount_document['amount']));
    }
}
