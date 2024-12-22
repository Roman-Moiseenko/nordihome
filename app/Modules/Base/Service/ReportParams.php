<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

class ReportParams
{
    public int $FIRST_START = 28; //Первая страница с блоком подписи
    public int $FIRST_FINISH = 35; //Первая страница без блока подписи, только с итого по стр.
    public int $NEXT_START = 33; //Последующие страницы с блоком подписи
    public int $NEXT_FINISH = 40;//Последующие страницы без блока подписи, только с итого по стр.
    public int $BEGIN_ROW = 11; //Первая позиция в списке

    public int $LEFT_COL = 2; //Номер первого столбца таблицы
    public int $RIGHT_COL = 10; //Номер последнего столбца таблицы

    public string $document = ''; //Название документа на новых страницах

    //TODO Данные шапки
    public int $HEADER_START = 9; //Строка начала шапки, 0 - без шапки
    public int $HEADER_FINISH = 10; //Строка окончания шапки

    public function __construct()
    {
    }
}
