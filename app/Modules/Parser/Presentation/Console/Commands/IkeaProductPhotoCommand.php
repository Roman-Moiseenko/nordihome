<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Application\Services\LoadParserProductIkeaService;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Infrastructure\Jobs\LoadProductPhotosIkeaJob;
use App\Modules\Shared\Application\Actions\GetGalleryThumbUseCase;
use Illuminate\Console\Command;

/**
 * Парсим новые (!) товары из каталогов Икеа
 */
class IkeaProductPhotoCommand extends Command
{
    use CreatesApplication;
    protected $signature = 'ikea:product-photos';
    protected $description = 'Парсим Товары Икеа';

    public function handle(
        LoadParserProductIkeaService $service,
        ParserProductRepositoryInterface $repositoryParserProduct,
        GetGalleryThumbUseCase $getGalleryThumbUseCase,
    ): void
    {

        $this->info('Парсим фото товаров');
        $ids = $repositoryParserProduct->getAllId();

        $index = 0;
        foreach ($ids as $id) {
            $array = $getGalleryThumbUseCase->execute($id, 'parser.product');
            if (empty($array)) {
                $this->info('id = ' . $id);
                $index++;
                LoadProductPhotosIkeaJob::dispatch($id);
            }
        }
        $this->info('Всего товаров без изображений = ' . $index);

    }
}
