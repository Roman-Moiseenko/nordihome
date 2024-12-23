<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\RefundDocument;
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

        //Заполняем общие статичные данные
        $replaceItems = [
            '{number}' => $refund->number,
            '{date}' => $refund->created_at->translatedFormat('d.m.Y'),
            //TODO ****
            '{trader.name}' => '',
            '{trader.address}' => '',
            '{trader.inn/kpp}' => '/',

            '{shopper.name}' => '',
            '{shopper.address}' => '',
            '{shopper.inn/kpp}' => '/',
            '{shopper.currency}' => '',

            '{status}' => '',
            '{chief}' => '',
            '{post}' => '',
            '{date.d}' => $refund->created_at->translatedFormat('d'),
            '{date.m}' => $refund->created_at->translatedFormat('MM'),
            '{date.y}' => $refund->created_at->translatedFormat('y'),

        ];

        return $this->SpreadSheet('utd', $replaceItems, $params, []); //$refund->products()->getModels()

    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        // TODO: Implement rowData() method.
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
