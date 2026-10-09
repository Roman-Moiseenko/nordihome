<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use Illuminate\Support\Facades\DB;

class ParserProductSearchQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'parser.product';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,
    )
    {
    }

    /**
     * Поиск товаров в таблице parser_products по частичному совпадению кода.
     *
     * Загружает товары и данные об изображении одним запросом.
     *
     * @param string $search Поисковый запрос, содержащий только цифры
     * @return array<int, array{id: int, name: string, url: string, code: string|null, image: string|null, price: float|null}>
     */
    public function getProductBySearch(string $search, int $limit): array
    {
        $rows = DB::table('parser_products')
            ->where('code', 'like', '%' . $search . '%')
            ->select(
                'parser_products.id',
                'parser_products.name',
                'parser_products.short',
                'parser_products.slug',
                'parser_products.code',
                'parser_products.price_sell',
                DB::raw("(SELECT id FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo_file"),
                DB::raw("(SELECT model_type FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' LIMIT 1) as model_type"),
            )
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => (int)$row->id,
                'name' => trim($row->name . (!empty($row->short) ? ' - ' . $row->short : '')),
                'url' => route('shop.ikea.product', $row->slug),
                'code' => codeIkea((string)$row->code),
                'image' => $this->imageThumbUseCase->execute($row, 'catalog'),
                'price' => (float)$row->price_sell,
            ];
        }

        return $result;
    }
}
