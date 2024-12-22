<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Base\Service\ReportParams;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArrivalReport extends AccountingReport
{

    protected ReportService $service;
    protected ReportParams $params;

    public function __construct(ReportService $service)
    {
        $this->service = $service;

        //Параметры шаблона
        $this->params = new ReportParams();
        $this->params->BEGIN_ROW = 11;
        $this->params->FIRST_START = 28;  //28
        $this->params->FIRST_FINISH = 35; //35
        $this->params->NEXT_START = 33; //33
        $this->params->NEXT_FINISH = 40; //40
        $this->params->LEFT_COL = 2;
        $this->params->RIGHT_COL = 8;
        $this->params->HEADER_START = 9;
        $this->params->HEADER_FINISH = 10;
    }

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
        //Создаем из шаблона пустой документ
        $arrival = ArrivalDocument::find($document_id);
        $template = $this->service->template('arrival');

        $spreadsheet = IOFactory::load($template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

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
        $this->service->findReplaceArray($activeWorksheet, $replaceItems);

        $this->params->document = 'Приходная накладная № ' . $arrival->number . ' от ' . $arrival->created_at->translatedFormat('d.m.Y');
        $this->service->createPages(
            $activeWorksheet, //&Страница
            $arrival->products()->getModels(), //Массив данных
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
