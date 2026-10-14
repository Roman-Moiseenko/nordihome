<?php

namespace App\Modules\Parser\Application\Interfaces;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;

interface ParserServiceInterface
{

    /// Категории ///
    /**
     * Получаем массив данных или ссылок категорий бренда (массив данных $categoryData)
     * @return array
     */
    public function getListCategories(): array;

    /**
     * Парсим данные о категории и добавляем в базу
     * @param $categoryData
     * @param int|null $parent_id
     * @return void
     */
    public function addCategory($categoryData, ?int $parent_id = null): void;


    /// Товары ///

    /**
     * Получаем данные о всех товарах в категории (код, ссылка в зависимости от сайта) массив данных $productData
     * @param string $categoryCode
     * @return array
     */
    public function GetListProductsByCategory(string $categoryCode): array;


    /**
     * На основе спарсенных данных создает Product и ParserProduct
     * @param $productData
     * @return ParserProductEntity|null
     */
    public function GetParserProduct($productData): ?ParserProductEntity;

    public function GetAvailability($productCate): void;


}
