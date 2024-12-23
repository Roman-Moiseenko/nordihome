<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

use App\Modules\Admin\Entity\Options;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportService
{
    public function template(string $name): string
    {
        $config = (new Options())->report[$name];
        return resource_path() . $config['template'];
    }

    /**
     * Сумма прописью
     */
    public function PriceToText(float $price, string $currency = null): string
    {
        $nul = 'ноль';
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
        $unit = [
            ['копейка', 'копейки', 'копеек', 1],
            ['рубль', 'рубля', 'рублей', 0],
            ['тысяча', 'тысячи', 'тысяч', 1],
            ['миллион', 'миллиона', 'миллионов', 0],
            ['миллиард', 'миллиарда', 'миллиардов', 0],
        ];


        if (strpos((string)$price, '.') != false) {
            list($rub, $kop) = array_pad(explode('.', sprintf("%015.2f", floatval($price))), 2, 0);
        } else {
            list($rub, $kop) = array_pad(explode(',', sprintf("%015.2f", floatval($price))), 2, 0);
        }
        $out = [];
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];

                list($i1, $i2, $i3) = array_pad(array_map('intval', str_split($v, 1)), 3, 0);
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        if (is_null($currency)) {
            $out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
            $out[] = $kop . ' ' . $this->morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        } else {
            $out[] = $currency;
        }
        $string = trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));

        return $this->firstUp($string);//mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1, mb_strlen($string));
    }

    /**
     * Склоняем словоформу
     */
    private function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }

    /**
     * Кол-во прописью
     */
    public function CountToText(int $number, bool $ne = false): string
    {
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одно', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = [10 => 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        $gender = $ne ? 1 : 0;

        $_h = intdiv($number, 100); //Сотни
        $_hd = $number % 100;
        if ($_hd > 19) {
            $_d = intdiv($_hd, 10); //Десятки
            $_e = $_hd % 10; //Единицы
            return $this->firstUp($hundred[$_h] . ' ' . $tens[$_d] . ' ' . $ten[$gender][$_e]);
        }
        if ($_hd > 9) {
            return $this->firstUp($hundred[$_h] . ' ' . $a20[$_hd]);
        }
        return $this->firstUp($hundred[$_h] . ' ' . $ten[$gender][$_hd]);
    }

    /**
     * Скопировать строку в Excel по номерам строк и столбцов
     */
    public function copyRows(Worksheet &$sheet, array $cellsStart, array $cellsEnd, array $dstCell): void
    {
        $destSheet = $sheet;

        $srcColumnStart = $cellsStart[0];
        $srcRowStart = $cellsStart[1];
        $srcColumnEnd = $cellsEnd[0];
        $srcRowEnd = $cellsEnd[1];

        $destColumnStart = $dstCell[0];
        $destRowStart = $dstCell[1];

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCell([$col, $row]);
                $style = $sheet->getStyle([$col, $row]);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $destSheet->setCellValue($dstCell, $cell->getValue());
                $destSheet->duplicateStyle($style, $dstCell);

                // Set width of column, but only once per column
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $destSheet->getColumnDimensionByColumn($destColumnStart + $colCount)->setAutoSize(false);
                    $destSheet->getColumnDimensionByColumn($destColumnStart + $colCount)->setWidth($w);
                }

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $destSheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                $targetRowStart = $destRowStart + $relativeRowStart;
                $targetRowEnd = $destRowStart + $relativeRowEnd;

                $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                //Merge target cells
                $destSheet->mergeCells($merge);
            }
        }
    }

    /**
     * Скопировать строку в Excel по диапозону
     */
    public function copyRowsRange(Worksheet $sheet, $srcRange, $dstCell, Worksheet $destSheet = null): void
    {
        if (!isset($destSheet)) $destSheet = $sheet;
        if (!preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch)) return;
        if (!preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch)) return;

        $srcColumnStart = $srcRangeMatch[1];
        $srcRowStart = (int)$srcRangeMatch[2];
        $srcColumnEnd = $srcRangeMatch[3];
        $srcRowEnd = (int)$srcRangeMatch[4];

        $destColumnStart = $destCellMatch[1];
        $destRowStart = (int)$destCellMatch[2];

        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd);
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);

        $this->copyRows($sheet, [$srcColumnStart, $srcRowStart], [$srcColumnEnd, $srcRowEnd], [$destColumnStart, $destRowStart]);
    }

    private function firstUp(string $string): string
    {
        $string = trim($string);
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1, mb_strlen($string));
    }

    /**
     * Замена данных в ячейках Excel из массива ["{key}" => "value"]
     */
    public function findReplaceArray(Worksheet &$activeWorksheet, array $items, int $rows = 45, int $cols = 90): void
    {
        for ($row = 1; $row < $rows; $row++) {
            for ($col = 1; $col < $cols; ++$col) {
                $cell_data = $activeWorksheet->getCell([$col, $row])->getValue();

                if (!is_null($cell_data)) {
                    foreach ($items as $key => $value)
                        $cell_data = str_replace($key, (string)$value, (string)$cell_data);
                    $activeWorksheet->setCellValue([$col, $row], $cell_data);
                }
            }
        }
    }

    /**
     * Разбивка по странично
     */
    public function getList(int $count, ReportParams $params): array
    {
        $result = [];
        while ($count > 0) {
            if (count($result) == 0) { //Первый лист
                if ($count > $params->FIRST_FINISH) {
                    $result[] = $params->FIRST_FINISH;
                    $count -= $params->FIRST_FINISH;
                } elseif ($count > $params->FIRST_START) {
                    $result[] = $params->FIRST_START;
                    $count -= $params->FIRST_START;
                } else {
                    $result[] = $count;
                    $count = 0;
                }
            } else { //Последующие листы
                if ($count > $params->NEXT_FINISH) {
                    $result[] = $params->NEXT_FINISH;
                    $count -= $params->NEXT_FINISH;
                }
                if ($count > $params->NEXT_START && $count <= $params->NEXT_FINISH) {
                    $result[] = $params->NEXT_START;
                    $count -= $params->NEXT_START;
                }
                if ($count <= $params->NEXT_START) {
                    $result[] = $count;
                    $count = 0;
                }
            }
        }

        return $result;
    }

    /**
     * Вставка копии строки
     */
    private function rowInsert(Worksheet &$activeWorksheet, int $row, ReportParams $params): void
    {
        $row++;
        $activeWorksheet->insertNewRowBefore($row, 1);
        $this->copyRows(
            $activeWorksheet,
            [$params->LEFT_COL, $params->BEGIN_ROW],
            [$params->RIGHT_COL, $params->BEGIN_ROW],
            [$params->LEFT_COL, $row]);
    }

    /**
     * Вставить пустую строку, со сбросом форматирования
     */
    public function rowInsertEmpty(Worksheet &$activeWorksheet, int $row, ReportParams $params, $count = 1): void
    {
        $styleBackground = [
            'fill' => [
                'type' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFFFF'],

            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ];
        $activeWorksheet
            ->insertNewRowBefore($row + $count - 1, $count)
            ->getStyle([$params->LEFT_COL, $row, $params->RIGHT_COL, $row + $count - 1])
            ->applyFromArray($styleBackground);
    }

    /**
     * Вставка разделителя страниц
     */
    private function rowPage(Worksheet &$activeWorksheet, int $row, $number_page, ReportParams $params): void
    {
        //Разрыв печати с предыдущей строки
        $activeWorksheet->setBreak('A' . ($row - 1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //Разделитель
        $this->rowInsertEmpty($activeWorksheet, $row, $params);
        $activeWorksheet->setCellValue([$params->LEFT_COL, $row], $params->document);
        $activeWorksheet->getStyle([$params->LEFT_COL, $row])
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $activeWorksheet->getStyle([$params->LEFT_COL, $row])->getFont()->setItalic(true);
        //Если есть свободная ячейка справа, то  объединяем со следующей для названия документа
        if ($params->RIGHT_COL - $params->LEFT_COL > 1)
            $activeWorksheet->mergeCells([$params->LEFT_COL, $row, $params->LEFT_COL + 1, $row]); //Проверить на всех шаблонах
        $activeWorksheet->setCellValue([$params->RIGHT_COL, $row], 'Страница ' . $number_page);
        $activeWorksheet->getStyle([$params->RIGHT_COL, $row])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeWorksheet->getStyle([$params->RIGHT_COL, $row])->getFont()->setItalic(true);
        /*  //Шапка*/
        $count_header = ($params->HEADER_FINISH - $params->HEADER_START) + 1;
        //$row += $count_header;
        $this->rowInsertEmpty($activeWorksheet, $row, $params, $count_header);
        $this->copyRows(
            $activeWorksheet,
            [$params->LEFT_COL, $params->HEADER_START],
            [$params->RIGHT_COL, $params->HEADER_FINISH],
            [$params->LEFT_COL, $row + 1]);
    }

    public function createPages(
        Worksheet    &$activeWorksheet,
        array        $list_items,
        ReportParams $params,
        callable     $rowData,
        callable     $emptyAmount,
        callable     $rowInterim,
        callable     $rowAmount,
    ): void
    {
        //Рассчитываем кол-во листов документа
        $pages = $this->getList(count($list_items), $params);

        $coef = 1; //Кол-во строк добавляемых для каждой страницы со второй. 1 - номер страницы
        if ($params->isInterim) $coef++;// +1 - итоги промежуточные,
        if ($params->HEADER_START != 0)
            $coef += ($params->HEADER_FINISH - $params->HEADER_START) + 1; // , +высота шапки

        $amountDocument = $emptyAmount();
        $start = 0;
        $row = $params->BEGIN_ROW;
        foreach ($pages as $n => $page) { //Разбиваем на страницы
            $amountPage = $emptyAmount(); //Обнуляем данные Итого по странице
            for ($position = $start; $position < $start + $page; $position++) {
                $row = $params->BEGIN_ROW + $position + $n * $coef;
                if ($position != count($list_items) - 1) $this->rowInsert($activeWorksheet, $row, $params);
                $rowData($activeWorksheet, $row, $position, $list_items[$position], $amountPage);
            }
            //Суммируем итого по страницам
            foreach ($amountPage as $key => $value) $amountDocument[$key] += $value;

            if ($n != count($pages) - 1) {//Промежуточная вставка с указанием номера страницы
                if ($params->isInterim) {
                    $row++;
                    $this->rowInsertEmpty($activeWorksheet, $row, $params);
                    $rowInterim($activeWorksheet, $row, $amountPage);//Итоги страницы
                }
                $row++;
                $this->rowPage($activeWorksheet, $row, $n + 2, $params); //Новая страница
                $start += $page;
            } else { //Итоговая вставка данных
                if ($params->isInterim) {
                    $row++;
                    $this->rowInsertEmpty($activeWorksheet, $row, $params);
                    $rowInterim($activeWorksheet, $row, $amountPage);
                }
                if ($params->isAmount) {
                    $row++;
                    $this->rowInsertEmpty($activeWorksheet, $row, $params);
                    $rowAmount($activeWorksheet, $row, $amountDocument);
                }
            }
        }
    }
}
