<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SupplyReport extends AccountingReport
{
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(): array
    {
        $items = [
            'reportNF62' => 'Заказ поставщику НФ-62 (xls)',
            'reportNF62PDF' => 'Заказ поставщику НФ-62 (pdf)',
            'reportNF62PDFSend' => 'Отправить поставщику НФ-62 (pdf)',
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

    public function reportNF62PDF(int $supply_id)
    {

    }

    public function reportNF62PDFSend(int $supply_id)
    {

    }

    private function xlsNF62(int $supply_id): Spreadsheet
    {
        $supply = SupplyDocument::find($supply_id);
        $template = $this->service->template('supply');

        $spreadsheet = IOFactory::load($template);
        $activeWorksheet = $spreadsheet->getActiveSheet();

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
            '{trader}' => '',
            '{distributor}' => $supply->organization->short_name,
            '{currency}' => $supply->currency->cbr_code,
            '{count}' => $supply->products()->count(),
        ];
        $this->service->findReplaceArray($activeWorksheet, $replaceItems);


        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);


        return $spreadsheet;
    }

}
