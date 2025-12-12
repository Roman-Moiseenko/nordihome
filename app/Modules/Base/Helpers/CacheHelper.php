<?php
declare(strict_types=1);

namespace App\Modules\Base\Helpers;

class CacheHelper
{
    const string MENU_CATEGORIES = 'menu_categories';
    const string MENU_TREES = 'menu_trees';

    const string CATEGORY = 'category-';
    const string PARSER_CATEGORY = 'parser-category-';
    const string CATEGORY_ATTRIBUTES = 'category-attributes-';
    const string CATEGORY_CHILDREN = 'category-children-';
    const string PARSER_CATEGORY_CHILDREN = 'parser-category-children-';
    const string CATEGORY_SCHEMA = 'category-schema-';
    const string CATEGORY_PRODUCTS = 'category-products-';
    const string PARSER_CATEGORY_PRODUCTS = 'parser-category-products-';

    const string PRODUCT_SCHEMA = 'product-scheme-';
    const string PRODUCT_CARD = 'product-card-';
    const string PRODUCT_VIEW = 'product-view-';
    const string PARSER_PRODUCT_CARD = 'parser-product-card-';
    const string PARSER_PRODUCT_CARD_VIEW = 'parser-product-view-';

    const array CATEGORIES = [
        self::CATEGORY,
        self::CATEGORY_SCHEMA,
        self::CATEGORY_ATTRIBUTES,
        self::CATEGORY_CHILDREN,
        self::CATEGORY_PRODUCTS,
    ];

    const array PRODUCTS = [
        self::PRODUCT_CARD,
        self::PRODUCT_SCHEMA,
        self::PRODUCT_VIEW,
    ];


}
