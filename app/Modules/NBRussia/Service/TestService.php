<?php

namespace App\Modules\NBRussia\Service;

use App\Modules\Base\Service\HttpPage;

class TestService
{

    private HttpPage $httpPage;

    public function __construct(HttpPage $httpPage)
    {
        $this->httpPage = $httpPage;
    }

    public function parser()
    {
       // $url = 'https://nbsklep.pl/api/graphql/frontend/menu/listing';
       // $url2= 'https://nbsklep.pl/api/graphql/frontend/lastViewed/3213770-3212449';
       // $json2 = '{"query":"\n    query lastViewed($ids: [ID!]!) {\n  productsByIds(ids: $ids) {\n    id\n    name\n    ...ListProductData\n  }\n}\n    \n    fragment ListProductData on ProductInterface {\n  niceUrl\n  picturesCategories {\n    slug\n    pictures {\n      filename\n      description\n    }\n  }\n  flags {\n    ...FlagData\n  }\n  prices {\n    ...PricesData\n  }\n  producer {\n    ...ProducerData\n  }\n  categoryPath {\n    id\n    name\n    niceUrl\n  }\n  reviewStats {\n    scoreCount\n    totalScore\n    averageScore\n    percentageScore\n  }\n  categories {\n    id\n    name\n    niceUrl\n  }\n  properties {\n    name\n    value {\n      ... on PropertyYesNoValue {\n        type\n        data\n      }\n      ... on PropertyOtherValue {\n        type\n        dataList: data\n      }\n    }\n  }\n}\n    \n\n    fragment FlagData on Flag {\n  id\n  name\n  symbol\n}\n    \n\n    fragment PricesData on Prices {\n  sellPrice {\n    ...PriceData\n  }\n  listPrice {\n    ...PriceData\n  }\n  basePrice {\n    ...PriceData\n  }\n  omnibusPrice {\n    ...PriceData\n  }\n}\n    \n\n    fragment PriceData on Price {\n  gross\n  nett\n  currency\n  vat\n  vatValue\n}\n    \n\n    fragment ProducerData on Producer {\n  id\n  name\n  description\n}\n    ","variables":{"ids":["3213770","3212449"]}}';
        // $data2 = Http::post($url);

        //Свойства
       /* $url = 'https://nbsklep.pl/api/graphql/frontend/productVariants/3214057';
        $json = '{"query":"\n    query productVariants($productId: ID!) {\n  variants(productId: $productId) {\n    ...VariantData\n  }\n}\n    \n    fragment VariantData on Variant {\n  id\n  option\n  ean\n  warehouseSymbol\n  skus\n  prices {\n    ...PricesData\n  }\n  availability {\n    buyable\n    message {\n      content\n      type\n    }\n  }\n  options {\n    id\n    groupId\n    name\n    value\n  }\n}\n    \n\n    fragment PricesData on Prices {\n  sellPrice {\n    ...PriceData\n  }\n  listPrice {\n    ...PriceData\n  }\n  basePrice {\n    ...PriceData\n  }\n  omnibusPrice {\n    ...PriceData\n  }\n}\n    \n\n    fragment PriceData on Price {\n  gross\n  nett\n  currency\n  vat\n  vatValue\n}\n    ","variables":{"productId":"3214057"}}';
        $data = $this->httpPage->post(url: $url, json: $json);
        dd($data);
        */

        //Варианты
     /*   $url = 'https://nbsklep.pl/api/graphql/frontend/productAssociations/3213902-product_colours-10'; //
        $json = '{"query":"\n    query productAssociations($id: ID!, $purposes: [String!]! = [], $limit: Int!) {\n  product(id: $id) {\n    associations(purposes: $purposes, limit: $limit) {\n      ...AssociationsData\n    }\n  }\n}\n    \n    fragment AssociationsData on AssociationsList {\n  header\n  purpose\n  products {\n    id\n    name\n    variants {\n      ...VariantData\n    }\n    ...ListProductData\n  }\n}\n    \n\n    fragment VariantData on Variant {\n  id\n  option\n  ean\n  warehouseSymbol\n  skus\n  prices {\n    ...PricesData\n  }\n  availability {\n    buyable\n    message {\n      content\n      type\n    }\n  }\n  options {\n    id\n    groupId\n    name\n    value\n  }\n}\n    \n\n    fragment PricesData on Prices {\n  sellPrice {\n    ...PriceData\n  }\n  listPrice {\n    ...PriceData\n  }\n  basePrice {\n    ...PriceData\n  }\n  omnibusPrice {\n    ...PriceData\n  }\n}\n    \n\n    fragment PriceData on Price {\n  gross\n  nett\n  currency\n  vat\n  vatValue\n}\n    \n\n    fragment ListProductData on ProductInterface {\n  niceUrl\n  picturesCategories {\n    slug\n    pictures {\n      filename\n      description\n    }\n  }\n  flags {\n    ...FlagData\n  }\n  prices {\n    ...PricesData\n  }\n  producer {\n    ...ProducerData\n  }\n  categoryPath {\n    id\n    name\n    niceUrl\n  }\n  reviewStats {\n    scoreCount\n    totalScore\n    averageScore\n    percentageScore\n  }\n  categories {\n    id\n    name\n    niceUrl\n  }\n  properties {\n    name\n    value {\n      ... on PropertyYesNoValue {\n        type\n        data\n      }\n      ... on PropertyOtherValue {\n        type\n        dataList: data\n      }\n    }\n  }\n}\n    \n\n    fragment FlagData on Flag {\n  id\n  name\n  symbol\n}\n    \n\n    fragment ProducerData on Producer {\n  id\n  name\n  description\n}\n    ","variables":{"id":"3213902","purposes":["product_colours"],"limit":10}}';
        $data = $this->httpPage->post(url: $url, json: $json);
        dd($data);*/
    /*    $url = 'https://nbsklep.pl/api/graphql/frontend/products/from_category-13043-2-';
        $json = '{"query":"\n    query products($objectType: ListingType!, $objectId: ID!, $page: Int!, $limit: Int, $sort: String, $filtersInput: FiltersInput) {\n  products(\n    id: $objectId\n    type: $objectType\n    limit: $limit\n    page: $page\n    sort: $sort\n    filters: $filtersInput\n  ) {\n    items {\n      id\n      name\n      pictures\n      reviewStats {\n        scoreCount\n        averageScore\n      }\n      variants {\n        ...VariantData\n      }\n      ...ListProductData\n    }\n    pagination {\n      ...PaginationData\n    }\n    filters {\n      ...FilterData\n    }\n  }\n}\n    \n    fragment VariantData on Variant {\n  id\n  option\n  ean\n  warehouseSymbol\n  skus\n  prices {\n    ...PricesData\n  }\n  availability {\n    buyable\n    message {\n      content\n      type\n    }\n  }\n  options {\n    id\n    groupId\n    name\n    value\n  }\n}\n    \n\n    fragment PricesData on Prices {\n  sellPrice {\n    ...PriceData\n  }\n  listPrice {\n    ...PriceData\n  }\n  basePrice {\n    ...PriceData\n  }\n  omnibusPrice {\n    ...PriceData\n  }\n}\n    \n\n    fragment PriceData on Price {\n  gross\n  nett\n  currency\n  vat\n  vatValue\n}\n    \n\n    fragment ListProductData on ProductInterface {\n  niceUrl\n  picturesCategories {\n    slug\n    pictures {\n      filename\n      description\n    }\n  }\n  flags {\n    ...FlagData\n  }\n  prices {\n    ...PricesData\n  }\n  producer {\n    ...ProducerData\n  }\n  categoryPath {\n    id\n    name\n    niceUrl\n  }\n  reviewStats {\n    scoreCount\n    totalScore\n    averageScore\n    percentageScore\n  }\n  categories {\n    id\n    name\n    niceUrl\n  }\n  properties {\n    name\n    value {\n      ... on PropertyYesNoValue {\n        type\n        data\n      }\n      ... on PropertyOtherValue {\n        type\n        dataList: data\n      }\n    }\n  }\n}\n    \n\n    fragment FlagData on Flag {\n  id\n  name\n  symbol\n}\n    \n\n    fragment ProducerData on Producer {\n  id\n  name\n  description\n}\n    \n\n    fragment PaginationData on Pagination {\n  itemsCount\n  lastPage\n  currentPage\n  itemsPerPage\n}\n    \n\n    fragment FilterData on Filter {\n  parentSelectedAttribute {\n    ...FilterAttributeData\n  }\n  attributes {\n    ...FilterAttributeData\n    children {\n      ...FilterAttributeData\n      children {\n        ...FilterAttributeData\n        children {\n          ...FilterAttributeData\n          children {\n            ...FilterAttributeData\n            children {\n              ...FilterAttributeData\n              children {\n                ...FilterAttributeData\n                children {\n                  ...FilterAttributeData\n                  children {\n                    ...FilterAttributeData\n                    children {\n                      ...FilterAttributeData\n                      children {\n                        ...FilterAttributeData\n                        children {\n                          ...FilterAttributeData\n                          children {\n                            ...FilterAttributeData\n                            children {\n                              ...FilterAttributeData\n                              children {\n                                ...FilterAttributeData\n                                children {\n                                  ...FilterAttributeData\n                                  children {\n                                    ...FilterAttributeData\n                                  }\n                                }\n                              }\n                            }\n                          }\n                        }\n                      }\n                    }\n                  }\n                }\n              }\n            }\n          }\n        }\n      }\n    }\n  }\n  id\n  type\n  name\n  labels\n  unit {\n    name\n    symbol\n  }\n}\n    \n\n    fragment FilterAttributeData on FilterAttribute {\n  bound\n  pictureUrl\n  title\n  value\n  name\n}\n    ","variables":{"objectType":"from_category","objectId":"13043","page":2}}';
        $data = $this->httpPage->post(url: $url, json: $json);
        dd($data);
*/
        /*$params = ['query' => "\n    query menu(\$symbol: String!) {\n  menu(symbol: \$symbol) {\n    id\n    labels\n    children {\n      ... on MenuChildrenInterface {\n        ...MenuChildrenData\n        ... on MenuChildrenInterface {\n          children {\n            ... on MenuChildrenInterface {\n              ...MenuChildrenData\n              ... on MenuChildrenInterface {\n                children {\n                  ... on MenuChildrenInterface {\n                    ...MenuChildrenData\n                    ... on MenuChildrenInterface {\n                      children {\n                        ... on MenuChildrenInterface {\n                          ...MenuChildrenData\n                          ... on MenuChildrenInterface {\n                            children {\n                              ... on MenuChildrenInterface {\n                                ...MenuChildrenData\n                              }\n                            }\n                          }\n                        }\n                      }\n                    }\n                  }\n                }\n              }\n            }\n          }\n        }\n      }\n    }\n  }\n}\n    \n    fragment MenuChildrenData on MenuChildrenInterface {\n  kind\n  labels\n  picture\n  title\n  ... on MenuChildrenLink {\n    href\n  }\n  ... on MenuChildrenSnippet {\n    content\n  }\n  ... on MenuChildrenCategory {\n    niceUrl\n  }\n}\n    ",
            "variables" => ["symbol" => "listing"],
        ];
        */
        //$json = '{"query":"\n    query menu($symbol: String!) {\n  menu(symbol: $symbol) {\n    id\n    labels\n    children {\n      ... on MenuChildrenInterface {\n        ...MenuChildrenData\n        ... on MenuChildrenInterface {\n          children {\n            ... on MenuChildrenInterface {\n              ...MenuChildrenData\n              ... on MenuChildrenInterface {\n                children {\n                  ... on MenuChildrenInterface {\n                    ...MenuChildrenData\n                    ... on MenuChildrenInterface {\n                      children {\n                        ... on MenuChildrenInterface {\n                          ...MenuChildrenData\n                          ... on MenuChildrenInterface {\n                            children {\n                              ... on MenuChildrenInterface {\n                                ...MenuChildrenData\n                              }\n                            }\n                          }\n                        }\n                      }\n                    }\n                  }\n                }\n              }\n            }\n          }\n        }\n      }\n    }\n  }\n}\n    \n    fragment MenuChildrenData on MenuChildrenInterface {\n  kind\n  labels\n  picture\n  title\n  ... on MenuChildrenLink {\n    href\n  }\n  ... on MenuChildrenSnippet {\n    content\n  }\n  ... on MenuChildrenCategory {\n    niceUrl\n  }\n}\n    ","variables":{"symbol":"listing"}}';
        //$data = $this->httpPage->post(url: $url, json: $json);
        $data = $this->httpPage->getPage('https://nbsklep.pl/meskie/obuwie/zimowe');
      //  $data = $this->httpPage->getPage('https://nbsklep.pl/damskie/obuwie/klasyczne/seria_530');

      //  preg_match_all('~data-qa-product_id="(.+?)"~', $data, $res);
       // preg_match_all('~<script id="__NEXT_DATA__" type="(.+?)">~', $data, $res);
        $begin = strpos($data, '<script id="__NEXT_DATA__" type="application/json">');
       // preg_match_all('~<script id="__NEXT_DATA__" type="application/json">(.+?)</script>~', $data, $res);
       // $data = $res[1][0];
        $text = substr($data, $begin + strlen('<script id="__NEXT_DATA__" type="application/json">'));
        $end = strpos($text, '</script>');
        $newData = substr($text, 0, $end);
        $array = json_decode($newData, true);
//
        $queries = $array['props']['pageProps']['dehydratedState']['queries'];
        $products = [];
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['products']))
                $products = $query['state']['data']['products']['items'];
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }

        dd($products);
    }
}
