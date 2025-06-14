<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Report;

use App\Modules\Base\Service\ReportParams;
use App\Modules\Base\Service\ReportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

abstract class AccountingReport
{
    protected string $path = '/report/accounting/';
    protected string $file = 'report';
    protected string $class = 'class';
    protected ReportService $service;

    //protected ReportParams $params;

    final public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    /**
     * Преобразование списка документов для frontend-компонента Accounting/Print.vue
     */
    final public function renderArray(array $items): array
    {
        $result = [];
        foreach ($items as $key => $value) {
            $result[] = [
                'method' => $key,
                'class' => static::class,
                'label' => $value,
            ];
        }
        return $result;
    }

    /** Отчет в Excel */
    final public function reportXLSX(int $document_id): string
    {
        set_time_limit(9000);
        $spreadsheet = $this->createSpreadSheet($document_id);
        $file = $this->generatePath($document_id, 'xlsx');

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) mkdir($path, 0777, true);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        set_time_limit(30);
        return $file;
    }

    /** Отчет в PDF */
    final public function reportPDF(int $document_id): string
    {
        $spreadsheet = $this->createSpreadSheet($document_id);
        $file = $this->generatePath($document_id, 'pdf');

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) mkdir($path, 0777, true);

        $writer = new Mpdf($spreadsheet);
        $writer->save($file);
        return $file;
    }

    /** Полное имя файла отчета */
    final protected function generatePath(int $id, string $ext): string
    {
        return storage_path() . $this->path . '/' . $this->class . '/' . $id . '/' . $this->file . '.' . $ext;
    }

    /** Формирование документа отчета */
    final protected function SpreadSheet(string $template, array $replaceItems, ReportParams $params, $items): Spreadsheet
    {
        set_time_limit(300);
        $template = $this->service->template($template); //Шаблон из файла, пути в config\shop.php

        $spreadsheet = IOFactory::load($template);
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $this->service->findReplaceArray($activeWorksheet, $replaceItems); //Замена статичных данных

        $this->service->createPages(
            $activeWorksheet, //&Страница
            $items, //Массив данных
            $params, //Параметры шаблона
            //Кэллбеки
            [$this, 'rowData'],
            [$this, 'emptyAmount'],
            [$this, 'rowInterim'],
            [$this, 'rowAmount'],
        );
        set_time_limit(30);
        return $spreadsheet;
    }

    /**
     * Список выходных документов ['Метод класса' => 'Название документа']
     */
    abstract public function index(): array;

    /**
     * Создание выходного документа
     */
    abstract protected function createSpreadSheet(int $document_id): Spreadsheet;

    /**
     * Вставка строки с данными
     * @param Worksheet $activeWorksheet Активный лист
     * @param int $row Строка вставки
     * @param int $position Позиция в документе
     * @param mixed $item Объект для вставки данных
     * @param array $amount_page суммирование промежуточных итогов по странично
     * @return void
     */
    abstract public function rowData(Worksheet &$activeWorksheet, int $row, int $position, mixed $item, array &$amount_page): void;

    /**
     * Обнуление промежуточных итогов
     * @return array
     */
    abstract public function emptyAmount(): array;

    /**
     * Вставка промежуточных итогов
     * @param Worksheet $activeWorksheet
     * @param int $row
     * @param array $amount_page
     * @return void
     */
    abstract public function rowInterim(Worksheet &$activeWorksheet, int $row, array $amount_page): void;

    /**
     * Вставка итогов по документу
     * @param Worksheet $activeWorksheet
     * @param int $row
     * @param array $amount_document
     * @return void
     */
    abstract public function rowAmount(Worksheet &$activeWorksheet, int $row, array $amount_document): void;
}
