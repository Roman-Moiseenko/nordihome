#Товар для карточки в списке

Product = [
    'id' => integer,
    'code' => Артикул с разделителями,
    'name' => Название товара,
    'slug' => ,
    'has_promotion' => true - если по акции,
    'is_new' => true - Новый,
    'is_wish' => true в Избранном,
    'is_sale' => Снят с проджаи,
    'rating' => Рейтинг по отзывам,
    'count_reviews' => Кол-во отзывов,
    'price' => Текущая цена для текущего пользователя,
    'price_promotion' => Цена по акции если есть, или 0,
    'images' => [
        'catalog-watermark' => ImageData,
    ],
    'images-next' => [
        'catalog-watermark' => ImageData,
    ],
    'modification' => null ?? Modification,
]

ImageData = [
    'src' => ссылка на изображение
    'alt' => Alt изображения
    'title' => Заголовок изображения
    'description' => Описание изображения
];
Modification = [
    'base_product' => $modification->base_product,
    'attributes' => array_map(function (Attribute $attribute) {
    return [
        'id' => $attribute->id,
        'name' => $attribute->name,
        'image' => $attribute->getImage(),
        'variants' => $attribute->variants()->get()->map(function (AttributeVariant $variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'image' => $variant->getImage(),
            ];
        }),
    ];
    }, $modification->prod_attributes),
    'products' => $modification->products()->get()->map(function (Product $product) {
    $values = json_decode($product->pivot->values_json, true);
    $variants = [];
    foreach ($values as $attr_id => $variant_id) {
    $variants[] = $product->getProdAttribute($attr_id)->getVariant($variant_id)->name;
    }
    return array_merge($product->toArray(), [
        'image' => $product->miniImage(),
        'variants' => $variants,
    ]);
    }),
]
