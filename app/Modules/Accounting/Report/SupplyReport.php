<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Base\Service\ReportInterface;
use App\Modules\Base\Service\ReportParams;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class SupplyReport extends AccountingReport implements ReportInterface
{
    private ReportService $service;

    private ReportParams $paramsNF62;


    public function __construct(ReportService $service)
    {
        $this->service = $service;

        //Параметры шаблона
        $this->paramsNF62 = new ReportParams();
        $this->paramsNF62->BEGIN_ROW = 11;
        $this->paramsNF62->FIRST_START = 28;  //28
        $this->paramsNF62->FIRST_FINISH = 35; //35
        $this->paramsNF62->NEXT_START = 33; //33
        $this->paramsNF62->NEXT_FINISH = 40; //40
        $this->paramsNF62->LEFT_COL = 2;
        $this->paramsNF62->RIGHT_COL = 8;
        $this->paramsNF62->HEADER_START = 9;
        $this->paramsNF62->HEADER_FINISH = 10;
    }

    public function index(): array
    {
        $items = [
            'reportNF62' => 'Заказ поставщику НФ-62 (xls)',
            'reportNF62PDF' => 'Заказ поставщику НФ-62 (pdf)',
        //    'reportNF62PDFSend' => 'Отправить поставщику НФ-62 (pdf)',
        ];
        return $this->renderArray($items);
    }

    public function reportNF62(int $supply_id): string
    {
        $spreadsheet = $this->xlsNF62($supply_id);
        $file = storage_path() . '/report/accounting/supply/' . $supply_id . '/nf62.xlsx';

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        return $file;
    }

    public function reportNF62PDF(int $supply_id): string
    {
        $spreadsheet = $this->xlsNF62($supply_id);
        $file = storage_path() . '/report/accounting/supply/' . $supply_id . '/nf62.pdf';
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $writer = new Mpdf($spreadsheet);
        $writer->save($file);
        return $file;
    }

    public function reportNF62PDFSend(int $supply_id)
    {

    }

    private function xlsNF62(int $supply_id): Spreadsheet
    {
        //Создаем из шаблона пустой документ
        $supply = SupplyDocument::find($supply_id);
        $template = $this->service->template('supply');

        $spreadsheet = IOFactory::load($template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

        //Заполняем общие статичные данные
        $amount_total = $supply->getAmount();
        $amount_vat = $supply->getAmountVAT();
        $trader = [
            $supply->customer->full_name,
            'ИНН ' . $supply->customer->inn,
            'КПП ' . $supply->customer->kpp,
            $supply->customer->legal_address->post . ', ' . $supply->customer->legal_address->address,
            phone($supply->customer->phone)
        ];

        $replaceItems = [
            '{number}' => $supply->number,
            '{date}' => $supply->created_at->translatedFormat('d.m.Y'),
            '{amount}' => $amount_total - $amount_vat,
            '{amount_vat}' => $amount_vat,
            '{amount_total}' => $amount_total,
            '{amount_text}' => $this->service->PriceToText($amount_total, $supply->currency->sign),
            '{staff}' => $supply->staff->fullname->getShortname(),
            '{trader}' => implode(', ', $trader),
            '{distributor}' => $supply->organization->short_name,
            '{currency}' => $supply->currency->cbr_code,
            '{count}' => $supply->products()->count(),
        ];
        $this->service->findReplaceArray($activeWorksheet, $replaceItems);

        $this->paramsNF62->document = 'Заказ поставщику № ' . $supply->number . ' от ' . $supply->created_at->translatedFormat('d.m.Y');


        $this->service->createPages(
            $activeWorksheet, //&Страница
            $supply->products()->getModels(), //Массив данных
            $this->paramsNF62, //Параметры шаблона
            //Кэллбеки
            [$this, 'rowData'],
            [$this, 'emptyAmount'],
            [$this, 'rowInterim'],
            [$this, 'rowAmount'],
        );

        //Параметры для документа
       // $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        return $spreadsheet;
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        /** @var SupplyProduct $item */
        $amount = $item->quantity * $item->cost_currency;
        $activeWorksheet->setCellValue([2, $row], ($position + 1));
        $activeWorksheet->setCellValue([3, $row], $item->product->name);
        $activeWorksheet->setCellValue([4, $row], $item->product->code);
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
