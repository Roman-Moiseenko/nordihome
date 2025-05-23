<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Helpers\CacheHelper;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

//TODO Автоматизировать и брать данные из Таблиц и настроек

//TODO протестировать на хостинге
class Schema
{
    private string $url;
    private Trader $trader;

    public function __construct()
    {
        $this->url = route('shop.home');
        $this->trader = Trader::default();
    }

    public function ProductPage(array $product)
    {
        $id = route('shop.product.view', $product['slug']) . '/#' . $product['code'];
        $schema1 = $this->_webPage(
            $id,
            $product['created_at'], $product['updated_at'],
            true);

        $schema2 = $this->_Product($product);

        return $this->html($schema1) . PHP_EOL . $this->html($schema2);

    }

    public function HomePage()
    {
        $schema1 = $this->_webPage('store');
        $schema2 = $this->_Store();

        return $this->html($schema1) . PHP_EOL . $this->html($schema2);
    }

    public function CategoryPage(int $category_id)
    {
        $category = Category::find($category_id);
        $schema = $this->_SubCategories($category);
        return $this->html($schema);
    }

    public function CategoryProductsPage(Category $category): string
    {
        $schema = $this->_Products($category);
        return $this->html($schema);
    }


    private function html(array $array)
    {
        $array = array_filter($array, function ($item) {
            return !is_null($item);
        });
        $result = '<script type="application/ld+json" class="schemantra.com">' . PHP_EOL;
        $result .= json_encode($array) . PHP_EOL;
        $result .= '</script>';
        return $result;
    }

    public function BreadCrumbs($breadcrumbs)
    {
        $bread = [];
        $count = $breadcrumbs->count();
        foreach ($breadcrumbs as $index => $breadcrumb) {

            if (empty($breadcrumb->url) || $count == ($index + 1)) {
                $bread[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $breadcrumb->title,
                ];

            } else {
                $bread[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => [
                        "@id" => $breadcrumb->url,
                        "name" => ($index == 0) ? 'Главная' : $breadcrumb->title,
                    ],
                ];
            }
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => 'breadcrumb',
            'name' => 'Хлебные крошки',
            'itemListElement' => $bread,
        ];

        return $this->html($schema);
    }

    //TODO Брать из Базы

    private function _webPage(string $id = '', $created_at = null, $updated_at = null, bool $breadcrumb = false)
    {

        return [
            '@context' => 'https://schema.org',
            '@type' => "WebPage",
            'url' => $this->url,
            'datePublished' => $created_at ?? now(),
            'dateModified' => $updated_at ?? now(),
            'description' => '',
            'inLanguage' => 'ru-RU',
            /*
               'potentialAction' => [
                   '@type' => 'SearchAction',
                   'target' => [
                       '@type' => 'EntryPoint',
                       'urlTemplate' => 'https://nordihome.ru/search?q={search_term_string}'
                   ],
                   'query-input' => 'required name=search_term_string'
               ],
               */
            'provider' => $this->_Organization(),
            "breadcrumb" => $breadcrumb ? ["@id" => "breadcrumb"] : null,
            "mainEntity" => [
                "@id" => $id,
            ]
        ];
    }

    private function _Organization(): array
    {


       // return [];
        return [
            "@type" => "Organization",
            "@id" => "https://nordihome.ru/#organization",
            "name" => $this->trader->organization->short_name,
            "url" => route('shop.home'),
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://nordihome.com/wp-content/uploads/2023/07/logo-nordi-home-1.png",
                "contentUrl" => "https://nordihome.com/wp-content/uploads/2023/07/logo-nordi-home-1.png",
                "width" => 2047,
                "height" => 2141,
                "caption" => "NORDI Home"
            ],
            "contactPoint" => [
                "@type" => "ContactPoint",
                "contactOption" => "TollFree",
                "contactType" => "Customer Service",
                "telephone" => [$this->trader->organization->phone],//TODO несколько телефонов
            ],
            "address" => [
                "@type" => "PostalAddress",
                "postalCode" => $this->trader->organization->actual_address->post,
                "addressLocality" => $this->trader->organization->actual_address->region,
                "streetAddress" => $this->trader->organization->actual_address->address
            ],
        ];
    }

    private function _Offer(array $product)
    {

        //return [];
        //"offers" =>
        return [
            "@type" => "Offer",
            "acceptedPaymentMethod" => [
                [
                    "@type" => "PaymentMethod",
                    "@id" => "https://purl.org/goodrelations/v1#ByBankTransferInAdvance"
                ],
                [
                    "@type" => "PaymentMethod",
                    "@id" => "https://purl.org/goodrelations/v1#ByInvoice"
                ],
                [
                    "@type" => "PaymentMethod",
                    "@id" => "https://purl.org/goodrelations/v1#Cash"
                ],
            ],
            "deliveryLeadTime" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "DAY",
                "value" => "14"
            ],
            "inventoryLevel" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "NMP",
                "value" => $product['quantity'],
            ],
            "hasMerchantReturnPolicy" => [
                "@type" => "MerchantReturnPolicy",
                "applicableCountry" => "RU",
                "returnPolicyCategory" => "https://schema.org/MerchantReturnFiniteReturnWindow",
                "merchantReturnDays" => 60,
                "returnMethod" => "https://schema.org/ReturnByMail",
                "returnFees" => "https://schema.org/FreeReturn"
            ],
            "warranty" => [
                "@type" => "WarrantyPromise",
                "durationOfWarranty" => [
                    "@type" => "QuantitativeValue",
                    "unitCode" => "MON",
                    "value" => "12"
                ]
            ],
            "itemCondition" => "https://schema.org/NewCondition",
            "availableDeliveryMethod" => "https://schema.org/OnSitePickup",
            "potentialAction" => [
                "@type" => "https://schema.org/BuyAction"
            ],

            "price" => $product['price'],
            "priceCurrency" => "RUB",
            "availability" => 'https://schema.org/' . ($product['quantity'] == 0 ? 'InStock' : 'PreOrder'),
            "shippingDetails" => [
                "@type" => "OfferShippingDetails",
                "shippingRate" => [
                    "@type" => "MonetaryAmount",
                    "value" => 0.0,
                    "currency" => "RUB"
                ],
                "shippingDestination" => [
                    "@type" => "DefinedRegion",
                    "addressCountry" => "RU",
                ],
                "deliveryTime" => [
                    "@type" => "ShippingDeliveryTime",
                    "handlingTime" => [
                        "@type" => "QuantitativeValue",
                        "minValue" => 5,
                        "maxValue" => 15,
                        "unitCode" => "DAY",
                    ],
                    "transitTime" => [
                        "@type" => "QuantitativeValue",
                        "minValue" => 5,
                        "maxValue" => 9,
                        "unitCode" => "DAY",
                    ],

                ],
            ],
            //TODO Данные из настроек текущей торговой организации
            /*
            "seller" => [
                "@type" => "Organization",
                "name" => "ООО «Негоциант»",
                "url" => "https://nordihome.ru/",
                "brand" => [
                    "@type" => "Brand",
                    "name" => "NORDI HOME",
                    "alternateName" => ["Евро Икеа", "Евроикеа", "euroikea", "euro ikea", "nordihome", "nordi home", "нордихом", "норди хом"],
                    "sameAs" => "https://vk.com/nordihome"
                ]
            ]
            */
        ];
    }

    private function _Store(): array
    {
        //TODO Данные из настроек текущей торговой организации
        return [];

        return [
            "@context" => "https://schema.org",
            "@type" => "Store",
            "@id" => $this->url . "/#store",
            "name" => "Магазин NORDI HOME - товары для дома",
            "image" => [
                "https://nordihome.ru/wp-content/uploads/2023/08/xxxl.jpg",
                "https://nordihome.ru/wp-content/uploads/2023/08/xxxl-1.jpg",
                "https://nordihome.ru/wp-content/uploads/2023/08/xxxl-2.jpg",
                "https://nordihome.ru/wp-content/uploads/2023/08/xxxl-4.jpg",
                "https://nordihome.ru/wp-content/uploads/2023/08/xxxl-3.jpg"
            ],
            "openingHours" => "ПН-ПТ - 10:00-19:00, СБ-ВС - 11:00-18:00",
            "openingHoursSpecification" => [
                "@type" => "OpeningHoursSpecification",
                "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                "opens" => "10:00",
                "closes" => "19:00"
            ],
            "paymentAccepted" => "Наличные, QR-код, Платежи СБП, Банковские карты, Оплата по счету",
            "priceRange" => "100 - 100 000 руб.",
            "telephone" => ["+7 (4012) 37-37-30", "+7 906 210-85-05"],
            "address" => [
                "@type" => "PostalAddress",
                "addressLocality" => "Калининград",
                "streetAddress" => "ул Советский проспект 103А корпус 1"
            ],
            "aggregateRating" => [
                "@type" => "aggregateRating",
                "ratingValue" => "5.0",
                "ratingCount" => "475",
                "url" => "https://www.avito.ru/user/767a54a084b8b382bc26e36a914ec5f7/profile/all?sellerId=767a54a084b8b382bc26e36a914ec5f7"
            ],
            "geo" => [
                "@type" => "GeoCoordinates",
                "latitude" => "54.737798",
                "longitude" => "20.477079"
            ],
            "brand" => [
                "@type" => "Brand",
                "name" => "NORDI HOME",
                "alternateName" => ["Евро Икеа", "Евроикеа", "euroikea", "euro ikea",
                    "nordihome", "nordi home", "нордихом", "норди хом", "нордихоум", "норди хоум", "нордик хоум",
                    "евроикея", "евро икея", "evroikea", "evro ikea"],
                "sameAs" => "https://vk.com/nordihome",
            ]
        ];
    }

    private function _Product(array $product)
    {
        $image = [];
        foreach ($product['gallery'] as $photo)
            $image[] = $photo['src'];

        $equivalents = null;

        foreach ($product['equivalents'] as $equivalent) {
            $equivalent[] = [
                "@type" => "Product",
                "name" => $equivalent['name'],
                "mainEntityOfPage" => route('shop.product.view', $equivalent['slug']),
                "sku" => $equivalent['code'],
            ];
        }

        return [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $product['name'],
            'description' => strip_tags($product['description']),
            'image' => $image,

            'url' => route('shop.product.view', $product['slug']),
            'sku' => $product['code'],
            'mpn' => $product['code'],
            'id' => route('shop.product.view', $product['slug']) . '/#' . $product['code'],
            'category' => [
                "@type" => "CategoryCode",
                "name" => $product['category']['name'],
                "url" => route('shop.category.view', $product['category']['slug']),
            ],
            /*
            //Атрибуты
            "depth" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "CMT",
                "value" => $product->dimensions->depth
            ],
            "width" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "CMT",
                "value" => $product->dimensions->width
            ],
            "height" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "CMT",
                "value" => $product->dimensions->height
            ],
            "weight" => [
                "@type" => "QuantitativeValue",
                "unitCode" => "KGM",
                "value" => $product->weight()
            ],
            */
            //TODO Серия, Цвета и другие Атрибуты ... из настроек???
            /*
                        'model' => is_null($product->series) ? null : [
                            "@type" => "ProductModel",
                            "name" => $product->series->name,
                            "aggregateRating" => ["@type" => "aggregateRating", "ratingValue" => "5", "ratingCount" => "70"]
                        ],*/
            'isSimilarTo' => $equivalents,

            //TODO Отзывы

            //TODO Рейтинг - на основе отзывов

            'offers' => $this->_Offer($product),
        ];
    }

    private function _SubCategories(Category $category)
    {
        $list = array_map(function (Category $category) {
            return [
                "@type" => "CollectionPage",
                "mainEntityOfPage" => route('shop.category.view', $category->slug),
                "name" => $category->name,
            ];
        },
            $category->children()->getModels()
        );

        return $this->_CollectionPage($list, $category);
    }

    private function _Products(Category $category): array
    {
        $list = array_map(function (Product $product) {
            return [

                "@type" => "Product",
                "mainEntityOfPage" => route('shop.product.view', $product->slug),
                "url" => route('shop.product.view', $product->slug),
                "name" => $product->name,
                "image" => $product->getImage('card'),
                "description" => strip_tags($product->description),
                "Offers" => [
                    "@type" => "Offer",
                    "price" => $product->getPrice(),
                    "priceCurrency" => "RUB",
                    "availability" => "https://schema.org/" . ($product->getQuantitySell() == 0 ? 'InStock' : 'PreOrder'),
                ],
            ];
        },
            $category->products()->where('published', true)->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            })->getModels()
        );
        return $this->_CollectionPage($list, $category);
    }

    private function _CollectionPage(array $list, Category $category)
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "url" => $this->url,
            "datePublished" => now(),
            "dateModified" => now(),
            "description" => "",
            "inLanguage" => "ru-RU",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => $this->url . "/search?q={search_term_string}",
                ],
                "query-input" => "required name=search_term_string"
            ],
            "provider" => $this->_Organization(),
            "breadcrumb" => ["@id" => "breadcrumb"],
            "mainEntity" => [
                "@type" => "ItemList",
                "numberOfItems" => count($list),
                "itemListOrder" => "https://schema.org/ItemListOrderDescending",
                "name" => "Товары и подкатегории " . $category->name,
                "itemListElement" => $list,
            ],
        ];
    }


}
