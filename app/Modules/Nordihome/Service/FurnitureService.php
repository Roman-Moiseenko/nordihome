<?php

namespace App\Modules\Nordihome\Service;

use App\Modules\Base\Service\HttpPage;

use DOMDocument;
use Illuminate\Support\Facades\Log;

class FurnitureService
{
    const string HOLZ_URL = 'https://holzmaster.ru/search/?q=';
    const string BALT_URL = 'https://baltlaminat.ru/catalog/?q=';
    private HttpPage $httpPage;

    //TODO Проверить ZAA.532C.BL W_R737

    public function __construct()
    {
        $this->httpPage = new HttpPage(); //Без кеша
    }

    public function getHolzMaster(string $code): float
    {
        $data = $this->httpPage->getPage(self::HOLZ_URL . $code, '', true);
        preg_match_all('/<a class="item" href="(.+?)">/s',$data, $result);
        if (empty($result[1])) {
            return 0.0;
        }

        foreach ($result[1] as $url) {
            $data = $this->httpPage->getPage('https://holzmaster.ru' . $url, '' , true);
            //Находим div с артикулом
            preg_match_all('~<div class="detail__data">(.+?)</div>~s', $data, $res_div);
            //Убираем лишние символы
            $text = $this->trim_iconv($res_div[1][0]);
            //Блок с "Артикул"
            preg_match_all('~<td width="50%">Артикул: </td><td width="50%">(.+?)</td>~s', $text, $sub_res);
            $find_code = $sub_res[1][0];
            if (str_contains($find_code, $code)) {
                //Ищем блок с ценой
                preg_match_all('~<div class="detail__price">(.+?)</div>~s', $data, $res_price);
                $text = $this->trim_iconv($res_price[1][0]);
                preg_match_all('~<span>(.+?)руб~s', $text, $sub_price);
                return (float)trim(str_replace(',', '.', $sub_price[1][0]));
            }
        }
            return 0.0;
    }

    public function getBaltlaminat(string $code): float
    {
        $find_code = str_replace(' ', '+', $code);
        $data = $this->httpPage->getPage(self::BALT_URL . $find_code, '', true);
        preg_match_all('~<div class="card__title-box">(.+?)</div>~s',$data, $result);
        if (empty($result[1])) {

            return 0.0;
        }

        foreach ($result[1] as $value) {
           // $text = $this->trim($value);

            preg_match_all('~<a href="(.+?)"~s', $value, $result);
            $url = $result[1][0];


            $data = $this->httpPage->getPage('https://baltlaminat.ru' . $url, '' , true);

            //TODO находим точное совпадение артикулов
            // Ищем блок div product-params
            preg_match_all('~<div class="product-params__t-line">.+?<p>.+?</p>.+?<p>(.+?)</p>.+?</div>~s', $data, $product_lines);

            if (empty($product_lines[1])) {

                return 0.0;
            }

            $product_lines = array_map(function ($item) {return $this->trim($item);}, $product_lines[1]);

            //В нем вытаскиваем артикул до 1го пробела и сравниваем полное совпадение
            foreach ($product_lines as $product_line)
            {
                if (mb_strpos($product_line, $code) === 0) {
                    $last = mb_substr($product_line, mb_strlen($code));

                    //TODO Если первый символ пробел или mb_strlen($last) == 0 то нашли, иначе пропуск
                    //dd(mb_strpos($last, ' '));
                    //dd(mb_strpos($last, ' '));
                    if (mb_strlen($last) == 0 || mb_strpos($last, ' ') === 0) {//Ищем блок с ценой
                        //dd("нашли");
                        preg_match_all('~<strong itemprop="price">(.+?)</strong>~s', $data, $res_price);
                        $data = str_replace(',', '.', $res_price[1][0]);
                        $data = str_replace(' ', '', $data);
                        return (float)trim($data);
                    }
                }

            }


/*
            preg_match_all('~<div class="product-params">(.+?)</div>~s', $data, $res_div);
            $text = $this->trim($res_div[1][0]);
           // dd([$res_div[1][0], $code]);
            if (str_contains($text, $code)) {
            }
*/
        }

        return 0.0;
    }

    private function trim_iconv(string $text): string
    {
  //      $text = str_replace(" ", '', $text);
        //$text = str_replace("\t", '', $text);
        //$text = str_replace("\n", '', $text);
        //Преобразуем кодировку
        return iconv('windows-1251', 'UTF-8', $this->trim($text));
    }

    private function trim(string $text): string
    {
        $text = str_replace("\t", '', $text);
        $text = str_replace("\n", '', $text);
        //Преобразуем кодировку
        return trim($text);
    }
}
