<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

class SupplyReport extends AccountingReport
{
    public function index(): array
    {
        $items = [
            'reportNF62' => 'Заказ поставщику НФ-62 (xls)',
            'reportNF62PDF' => 'Заказ поставщику НФ-62 (pdf)',
            'reportNF62PDFSend' => 'Отправить поставщику НФ-62 (pdf)',
        ];
        return $this->renderArray($items);
    }

    public static function reportNF62(int $supply_id)
    {

    }

    public static function reportNF62PDF(int $supply_id)
    {

    }

    public static function reportNF62PDFSend(int $supply_id)
    {

    }
}
