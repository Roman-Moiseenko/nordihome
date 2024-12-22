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

    protected ReportService $service;
    protected ReportParams $params;
    protected string $class = 'surplus';

    public function __construct(ReportService $service)
    {
        $this->service = $service;

        //Параметры шаблона
        $this->params = new ReportParams();
        $this->params->BEGIN_ROW = 8;
        $this->params->FIRST_START = 28;
        $this->params->FIRST_FINISH = 35;
        $this->params->NEXT_START = 33;
        $this->params->NEXT_FINISH = 40;
        $this->params->LEFT_COL = 2;
        $this->params->RIGHT_COL = 8;
        $this->params->HEADER_START = 6;
        $this->params->HEADER_FINISH = 7;
    }

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
        //Создаем из шаблона пустой документ
        /** @var SurplusDocument $surplus */
        $surplus = SurplusDocument::find($document_id);
        $template = $this->service->template('surplus');

        $spreadsheet = IOFactory::load($template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

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
        $this->service->findReplaceArray($activeWorksheet, $replaceItems);

        $this->params->document = 'Акт об оприходовании запасов № ' . $surplus->number . ' от ' . $surplus->created_at->translatedFormat('d.m.Y');
        $this->service->createPages(
            $activeWorksheet, //&Страница
            $surplus->products()->getModels(), //Массив данных
            $this->params, //Параметры шаблона
            //Кэллбеки
            [$this, 'rowData'],
            [$this, 'emptyAmount'],
            [$this, 'rowInterim'],
            [$this, 'rowAmount'],
        );
        return $spreadsheet;
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
        $activeWorksheet->setCellValue([4, $row], 'На странице');
        $activeWorksheet->setCellValue([5, $row], $amount_page['quantity']);
        $activeWorksheet->setCellValue([8, $row], $amount_page['amount']);
    }

    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void
    {
        $activeWorksheet->mergeCells([6, $row, 7, $row]);
        $activeWorksheet->getStyle([4, $row, 8, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $activeWorksheet->setCellValue([4, $row], 'Всего');
        $activeWorksheet->setCellValue([5, $row], $amount_document['quantity']);
        $activeWorksheet->setCellValue([8, $row], $amount_document['amount']);
    }
}
