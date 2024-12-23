<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Base\Service\ReportParams;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartureReport extends AccountingReport
{
    protected string $class = 'departure';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Списание запасов (xls)',
            'reportPDF' => 'Списание запасов (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $departure = DepartureDocument::find($document_id);
        $params = new ReportParams(12, 28, 35, 33, 40,
            2, 8, 10, 11,
            'Списание запасов № ' . $departure->number . ' от ' . $departure->created_at->translatedFormat('d.m.Y')
        );

        //Заполняем общие статичные данные
        $amount = $departure->getAmount();

        $replaceItems = [
            '{number}' => $departure->number,
            '{date}' => $departure->created_at->translatedFormat('d.m.Y'),
            '{customer}' => $departure->customer->short_name,
            '{reason}' => is_null($departure->inventory)
                ? $departure->comment
                : 'Инвентаризация № ' . $departure->inventory->number . ' от ' . $departure->inventory->created_at->translatedFormat('d-m-Y'),
            '{storage}' => $departure->storage->address,
            '{staff}' => $departure->staff->fullname->getShortname(),
            '{count}' => $departure->products()->count(),
            '{amount}' => $amount,
            '{amount_text}' => $this->service->PriceToText($amount),
        ];

        return $this->SpreadSheet('departure', $replaceItems, $params, $departure->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var DepartureProduct $item */
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->code);
        $activeWorksheet->setCellValue([4, $row], $item->product->name);
        $activeWorksheet->setCellValue([5, $row], $item->quantity);
        $activeWorksheet->setCellValue([6, $row], $item->product->measuring->name);
        $activeWorksheet->setCellValue([7, $row], $item->cost);
        $activeWorksheet->setCellValue([8, $row], $item->quantity * $item->cost);
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
