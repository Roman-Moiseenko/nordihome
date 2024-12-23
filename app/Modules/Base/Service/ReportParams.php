<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

class ReportParams
{

    public int $BEGIN_ROW = 11; //Первая позиция в списке
    public int $FIRST_START = 28; //Первая страница с блоком подписи
    public int $FIRST_FINISH = 35; //Первая страница без блока подписи, только с итого по стр.
    public int $NEXT_START = 33; //Последующие страницы с блоком подписи
    public int $NEXT_FINISH = 40;//Последующие страницы без блока подписи, только с итого по стр.

    public int $LEFT_COL = 2; //Номер первого столбца таблицы
    public int $RIGHT_COL = 10; //Номер последнего столбца таблицы

    public int $HEADER_START = 9; //Строка начала шапки, 0 - без шапки
    public int $HEADER_FINISH = 10; //Строка окончания шапки

    public string $document = ''; //Название документа на новых страницах

    public bool $isInterim = true; //Добавлять строку промежуточных итогов
    public bool $isAmount = true; //Добавлять итоговую строку

    public function __construct(int $BEGIN_ROW = 11,
                                int $FIRST_START = 28, int $FIRST_FINISH = 35,
                                int $NEXT_START = 33, int $NEXT_FINISH = 40,
                                int $LEFT_COL = 2, int $RIGHT_COL = 10,
                                int $HEADER_START = 9, int $HEADER_FINISH = 10,
                                string $document = '',
    )
    {
        $this->FIRST_START = $FIRST_START;
        $this->FIRST_FINISH = $FIRST_FINISH;
        $this->NEXT_START = $NEXT_START;
        $this->NEXT_FINISH = $NEXT_FINISH;
        $this->BEGIN_ROW = $BEGIN_ROW;
        $this->LEFT_COL = $LEFT_COL;
        $this->RIGHT_COL = $RIGHT_COL;
        $this->HEADER_START = $HEADER_START;
        $this->HEADER_FINISH = $HEADER_FINISH;
        $this->document = $document;
    }

    public static function utd(): self
    {
        return new self(20, 25, 12, 22, 42,
            1, 88, 17, 19,
            'УПД'
        );
    }

    public function notInterim()
    {
        $this->isInterim = false;
    }

    public function notAmount()
    {
        $this->isAmount = false;
    }
}
