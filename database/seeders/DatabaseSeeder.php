<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Entity\Dimensions;
use App\Entity\User\FullName;
use App\Entity\User\User;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\AttributeGroupRepository;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Product\Repository\BrandRepository;
use App\Modules\Product\Repository\CategoryRepository;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private CategoryRepository $categoryRepository;
    private AttributeGroupRepository $attributeGroupRepository;
    private AttributeRepository $attributeRepository;
    private BrandRepository $brandRepository;


    /**
     * Seed the application's database.
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        AttributeGroupRepository $attributeGroupRepository,
        AttributeRepository $attributeRepository,
        BrandRepository $brandRepository,
    )
    {

        $this->categoryRepository = $categoryRepository;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->attributeRepository = $attributeRepository;
        $this->brandRepository = $brandRepository;
    }

    public function run(): void
    {

        $this->brand();

        $this->categories();

        $this->attributes();

        $this->products();
    }

    private function brand()
    {
        $brands = [
            [
                'name' => 'Икеа',
                'description' => 'Известный шведский бренд, производитель мебели и товаров для дома',
                'url' => 'https://ikea.com',
                'sameAs' => [
                    'https://ikea.pl',
                    'https://wikipedia.com/ikea',
                    'https://ikea.ru',
                ],
            ],
            [
                'name' => 'Adidas',
                'description' => 'Бренд спортивной обуви и одежды из Японии',
                'url' => 'https://adidas.com',
                'sameAs' => [
                    'https://adidas.com',
                    'https://wikipedia.com/adidas',
                    'https://adidas.ru',
                ],
            ],
            [
                'name' => 'Nokia',
                'description' => 'Финский производитель мобильных телефонов и сетевого оборудования',
                'url' => 'https://nokia.com',
                'sameAs' => [
                    'https://nokia.com',
                    'https://wikipedia.com/nokia',
                    'https://nokia.ru',
                ],
            ],
        ];

        foreach ($brands as $brand) {
            $_brand = Brand::register($brand['name'], $brand['description'], $brand['url']);
            $_brand->setSameAs($brand['sameAs']);
            $_brand->save();
        }
    }

    private function categories()
    {
        $categories = [
            [
                'name' => 'Светильники',
                'subcategories' => [
                    ['name' => 'Торшеры',],
                    ['name' => 'Бра',],
                    ['name' => 'Подвесные',],
                    ['name' => 'Настольные',],
                    ['name' => 'Люстры',],
                    ['name' => 'Лампочки',],
                ],
            ],
            [
                'name' => 'Мебель',
                'subcategories' => [
                    [
                        'name' => 'Столы',
                        'subcategories' => [
                            ['name' => 'Кухонные',],
                            ['name' => 'Письменные',],
                            ['name' => 'Офисные',],
                        ],
                    ],
                    [
                        'name' => 'Кровати',
                    ],
                    [
                        'name' => 'Диваны',
                        'subcategories' => [
                            ['name' => 'Угловые',],
                            ['name' => 'Раскладные',],
                            ['name' => 'Детские',],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Посуда',
                'subcategories' => [
                    ['name' => 'Кастрюли',],
                    ['name' => 'Сервисы и наборы',],
                    ['name' => 'Столовые приборы',],
                    ['name' => 'Тарелки',],
                    ['name' => 'Кружки',],
                ],
            ],
            [
                'name' => 'Текстиль',
                'subcategories' => [
                    ['name' => 'Полотенца',],
                    ['name' => 'Постельное белье',],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $cat = Category::register($category['name']); //1 lvl
            if (isset($category['subcategories'])) {
                $subcategories = $category['subcategories'];
                foreach ($subcategories as $subcategory) {
                    $subcat = Category::register($subcategory['name'], $cat->id); //2 lvl
                    if (isset($subcategory['subcategories'])) {
                        $lastcategories = $subcategory['subcategories'];
                        foreach ($lastcategories as $lastcategory) {
                            Category::register($lastcategory['name'], $subcat->id); //3 lvl
                        }
                    }
                }
            }
        }
    }

    private function attributes()
    {
        $attributeGroups = [
            ['name' => 'Основные характеристики',],
            ['name' => 'Технические свойства',],
            ['name' => 'Комплектация',],
        ];

        foreach ($attributeGroups as $attributeGroup) {
            AttributeGroup::register($attributeGroup['name']);
        }

        $attributes = [
            [
                'name' => 'Цвета',
                'categories' => [
                    $this->categoryRepository->byName('Светильники')->id,
                    $this->categoryRepository->byName('Посуда')->id,
                    $this->categoryRepository->byName('Текстиль')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
                'multiple' => false,
                'variants' => [
                    'белый',
                    'черный',
                    'красный',
                    'серый',
                ],
            ],
            [
                'name' => 'Цвета',
                'categories' => [
                    $this->categoryRepository->byName('Мебель')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
                'multiple' => false,
                'variants' => [
                    'белый',
                    'бук',
                    'дуб',
                    'сосна',
                ],
            ],
            [
                'name' => 'Кол-во ламп',
                'categories' => [
                    $this->categoryRepository->byName('Светильники')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Комплектация')->id,
                'multiple' => false,
                'variants' => [
                    '1 лампа',
                    '2 лампы',
                    '3 лампы',
                    '4 лампы',
                ],
            ],
            [
                'name' => 'Влагозащищенный',
                'categories' => [
                    $this->categoryRepository->byName('Светильники')->id,
                ],
                'type' => Attribute::TYPE_BOOL,
                'group_id' => $this->attributeGroupRepository->byName('Технические свойства')->id,
            ],
            [
                'name' => 'Кол-во персон',
                'categories' => [
                    $this->categoryRepository->byName('Сервисы и наборы')->id,
                    $this->categoryRepository->byName('Столовые приборы')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Комплектация')->id,
                'multiple' => false,
                'variants' => [
                    '4 персоны',
                    '6 персон',
                    '8 персон',
                ],
            ],
            [
                'name' => 'Объем (л)',
                'categories' => [
                    $this->categoryRepository->byName('Кастрюли')->id,
                ],
                'type' => Attribute::TYPE_FLOAT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
            ],
            [
                'name' => 'Материал',
                'categories' => [
                    $this->categoryRepository->byName('Светильники')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
                'multiple' => true,
                'variants' => [
                    'металл',
                    'пластик',
                    'стекло',
                    'текстиль',
                    'винил',
                ],
            ],
            [
                'name' => 'Материал',
                'categories' => [
                    $this->categoryRepository->byName('Посуда')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
                'multiple' => false,
                'variants' => [
                    'металл',
                    'фарфор',
                    'стекло',
                    'дерево',
                ],
            ],
            [
                'name' => 'Патрон',
                'categories' => [
                    $this->categoryRepository->byName('Светильники')->id,
                ],
                'type' => Attribute::TYPE_VARIANT,
                'group_id' => $this->attributeGroupRepository->byName('Основные характеристики')->id,
                'multiple' => false,
                'variants' => [
                    'E14',
                    'E27',
                    'G4',
                    'G5.3',
                    'GU7.3',
                    'GU10',
                ],
            ],
        ];

        foreach ($attributes as $attribute) {
            /** @var Attribute $_attr */
            $_attr = Attribute::register(
                $attribute['name'],
                $attribute['group_id'],
                $attribute['type']
            );
            if ($_attr->isVariant()) {
                $_attr->multiple = $attribute['multiple'];
            }
            foreach ($attribute['categories'] as $category) {
                $_attr->categories()->attach((int)$category);
            }
            if ($attribute['type'] == Attribute::TYPE_VARIANT) {
                foreach ($attribute['variants'] as $variant) {
                    $_attr->addVariant($variant);
                }
            }
        }
    }

    private function products()
    {
        $dimension = Dimensions::create(
            30,
            30,
            30,
            5,
            Dimensions::MEASURE_KG
        );
        $products = [
            [
                'name' => 'Торшер INKUS 105B',
                'code' => '105B-001',
                'main_category_id' => $this->categoryRepository->byName('Торшеры')->id,
                'price' => 5500,
                'count' => 10,
                'frequency' => Product::FREQUENCY_AVERAGE,
                'brand' => $this->brandRepository->byName('Nokia')->id,
            ],
            [
                'name' => 'Торшер INKUS 305B',
                'code' => '305B-001',
                'main_category_id' => $this->categoryRepository->byName('Торшеры')->id,
                'price' => 7500,
                'count' => 10,
                'frequency' => Product::FREQUENCY_AVERAGE,
                'brand' => $this->brandRepository->byName('Nokia')->id,
            ],
            [
                'name' => 'Торшер INKUS 305W',
                'code' => '305W-001',
                'main_category_id' => $this->categoryRepository->byName('Торшеры')->id,
                'price' => 7500,
                'count' => 10,
                'frequency' => Product::FREQUENCY_AVERAGE,
                'brand' => $this->brandRepository->byName('Nokia')->id,
            ],
            [
                'name' => 'Торшер INKUS 205W',
                'code' => '205W-001',
                'main_category_id' => $this->categoryRepository->byName('Торшеры')->id,
                'price' => 5500,
                'count' => 10,
                'frequency' => Product::FREQUENCY_AVERAGE,
                'brand' => $this->brandRepository->byName('Nokia')->id,
            ],
            [
                'name' => 'Лампа светодиодная E14',
                'code' => '456-E14-001',
                'main_category_id' => $this->categoryRepository->byName('Лампочки')->id,
                'price' => 250,
                'count' => 100,
                'frequency' => Product::FREQUENCY_PERIOD,
                'brand' => $this->brandRepository->byName('Икеа')->id,
            ],
            [
                'name' => 'Лампа накаливания E14',
                'code' => '456-E14-002',
                'main_category_id' => $this->categoryRepository->byName('Лампочки')->id,
                'price' => 50,
                'count' => 1000,
                'frequency' => Product::FREQUENCY_PERIOD,
                'brand' => $this->brandRepository->byName('Икеа')->id,
            ],
        ];


        foreach ($products as $item) {
            $product = Product::register(
                $item['name'],
                $item['code'],
                $item['main_category_id'],
            );
            $product->dimensions = $dimension;

            $product->setPrice($item['price']);
            $product->count_for_sell = $item['count'];
            $product->brand_id = $item['brand'];
            $product->save();
            $product->update([
                'description' => 'Подробное описание товара с HTML-тегами',
                'short' => 'Краткое описание товара',
                'frequency' => $item['frequency']
            ]);
        }
        /*
                    [
                'name' => '',
                'code' => '',
                'main_category_id' => 0,
                'description' => 'Подробное описание товара с HTML-тегами',
                'short' => 'Краткое описание товара',
                'price' => 0,
                'count' => 0,
                'frequency' => Product::FREQUENCY_AVERAGE,
            ],
        */
    }
}
