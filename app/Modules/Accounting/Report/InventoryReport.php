<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\InventoryProduct;
use App\Modules\Base\Service\ReportParams;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReport extends AccountingReport
{
    protected string $class = 'inventory';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Инвентаризация запасов (xls)',
            'reportPDF' => 'Инвентаризация запасов (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $inventory = InventoryDocument::find($document_id);
        $params = new ReportParams(11, 26, 33, 30, 38,
            2, 11, 8, 10,
            'Списание запасов № ' . $inventory->number . ' от ' . $inventory->created_at->translatedFormat('d.m.Y')
        );

        //Заполняем общие статичные данные
        $amount = $inventory->getAmount();
        $replaceItems = [
            '{number}' => $inventory->number,
            '{date}' => $inventory->created_at->translatedFormat('d.m.Y'),
            '{customer}' => $inventory->customer->short_name,
            '{storage}' => $inventory->storage->address,
            '{staff}' => $inventory->staff->fullname->getShortname(),
            '{count}' => $inventory->products()->count(),
            '{amount}' => $amount,
            '{amount_text}' => $this->service->PriceToText($amount),
        ];

        return $this->SpreadSheet('inventory', $replaceItems, $params, $inventory->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var InventoryProduct $item */
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->code);
        $activeWorksheet->setCellValue([4, $row], $item->product->name);
        $activeWorksheet->setCellValue([5, $row], $item->quantity - $item->formal);
        $activeWorksheet->setCellValue([6, $row], $item->formal);
        $activeWorksheet->setCellValue([7, $row], $item->quantity);
        $activeWorksheet->setCellValue([8, $row], $item->product->measuring->name);
        $activeWorksheet->setCellValue([9, $row], $item->cost);
        $activeWorksheet->setCellValue([10, $row], $item->quantity * $item->cost);
        $activeWorksheet->setCellValue([11, $row], $item->formal * $item->cost);

        $amount_page['formal'] += $item->formal;
        $amount_page['quantity'] += $item->quantity;
        $amount_page['amount_formal'] += $item->formal * $item->cost;
        $amount_page['amount'] += $item->quantity * $item->cost;
    }

    public function emptyAmount(): array
    {
        return [
            'formal' => 0,
            'quantity' => 0,
            'amount_formal' => 0,
            'amount' => 0
        ];
    }

    public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void
    {
        $activeWorksheet->mergeCells([8, $row, 9, $row]);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], 'На странице');
        $activeWorksheet->setCellValue([5, $row], $amount_page['quantity'] - $amount_page['formal']);
        $activeWorksheet->setCellValue([6, $row], $amount_page['quantity']);
        $activeWorksheet->setCellValue([7, $row], $amount_page['formal']);
        $activeWorksheet->setCellValue([10, $row], price($amount_page['amount']));
        $activeWorksheet->setCellValue([11, $row], price($amount_page['amount_formal']));
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        $activeWorksheet->mergeCells([8, $row, 9, $row]);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeWorksheet->getStyle([4, $row, 11, $row])->getFont()->setBold(true);
        $activeWorksheet->setCellValue([4, $row], 'Всего');
        $activeWorksheet->setCellValue([5, $row], $amount_document['quantity'] - $amount_document['formal']);
        $activeWorksheet->setCellValue([6, $row], $amount_document['quantity']);
        $activeWorksheet->setCellValue([7, $row], $amount_document['formal']);
        $activeWorksheet->setCellValue([10, $row], price($amount_document['amount']));
        $activeWorksheet->setCellValue([11, $row], price($amount_document['amount_formal']));
    }
}
