<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface ReportInterface
{
    /**
     * Вставка строки с данными
     * @param Worksheet $activeWorksheet Активный лист
     * @param int $row Строка вставки
     * @param int $position Позиция в документе
     * @param mixed $item Объект для вставки данных
     * @param array $amount_page суммирование промежуточных итогов по странично
     * @return void
     */
    public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void;

    /**
     * Обнуление промежуточных итогов
     * @return array
     */
    public function emptyAmount(): array;

    /**
     * Вставка промежуточных итогов
     * @param Worksheet $activeWorksheet
     * @param int $row
     * @param array $amount_page
     * @return void
     */
    public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void;

    /**
     * Вставка итогов по документу
     * @param Worksheet $activeWorksheet
     * @param int $position
     * @param array $amount_document
     * @return void
     */
    public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void;
}
