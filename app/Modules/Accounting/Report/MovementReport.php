<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Base\Service\ReportParams;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementReport extends AccountingReport
{
    protected string $class = 'movement';

    public function index(): array
    {
        $items = [
            'reportXLSX' => 'Перемещение запасов (xls)',
            'reportPDF' => 'Перемещение запасов (pdf)',
        ];
        return $this->renderArray($items);
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        $movement = MovementDocument::find($document_id);
        $params = new ReportParams(10, 28, 35, 33, 40,
            2, 8, 8, 9,
            'Перемещение запасов № ' . $movement->number . ' от ' . $movement->created_at->translatedFormat('d.m.Y')
        );
        $params->notInterim();
        $params->notAmount();

        //Заполняем общие статичные данные
        $replaceItems = [
            '{number}' => $movement->number,
            '{date}' => $movement->created_at->translatedFormat('d.m.Y'),
            '{storage_out}' => $movement->storageOut->address,
            '{storage_in}' => $movement->storageIn->address,
            '{staff}' => $movement->staff->fullname->getShortname(),

        ];

        return $this->SpreadSheet('movement', $replaceItems, $params, $movement->products()->getModels());
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var MovementProduct $item */
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->code);
        $activeWorksheet->setCellValue([4, $row], $item->product->name);
        $activeWorksheet->setCellValue([5, $row], $item->quantity);
        $activeWorksheet->setCellValue([6, $row], $item->product->measuring->name);
        if (!is_null($item->orderItem)) {
            $order = '№ ' . $item->orderItem->order->number . ' от ' . $item->orderItem->order->htmlDate();
            $reserve = $item->orderItem->reserves()->first()->created_at->translatedFormat('d-m-Y H:i');
        } else {
            $order = '';
            $reserve = '';
        }
        $activeWorksheet->setCellValue([7, $row], $reserve); //Резерв
        $activeWorksheet->setCellValue([8, $row], $order); //Заказ
    }

    public function emptyAmount(): array
    {
        return [];
    }

    public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void
    {
        //Не используется
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        //Не используется
    }
}
