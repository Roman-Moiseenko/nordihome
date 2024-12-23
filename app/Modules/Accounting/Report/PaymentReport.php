<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReport extends AccountingReport
{

    public function __construct(ReportService $service)
    {

    }

    public function index(): array
    {
        // TODO: Implement index() method.
    }

    protected function createSpreadSheet(int $document_id): Spreadsheet
    {
        // TODO: Implement createSpreadSheet() method.
    }

    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void
    {
        // TODO: Implement rowData() method.
    }

    public function emptyAmount(): array
    {
        // TODO: Implement emptyAmount() method.
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
