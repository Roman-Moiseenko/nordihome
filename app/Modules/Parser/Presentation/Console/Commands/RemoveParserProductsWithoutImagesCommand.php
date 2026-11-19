<?php

declare(strict_types=1);

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shared\Application\Actions\GetPhotoByEntityUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class RemoveParserProductsWithoutImagesCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'parser:remove-products-without-images';

    protected $description = 'Удаляет товары парсера без изображений (полное удаление)';

    private const string MODEL_TYPE = 'parser.product';

    public function __construct(
        private readonly GetPhotoByEntityUseCase $getPhotoByEntityUseCase,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $products = ParserProduct::query()->get(['id']);

        $this->info('Товаров парсера: ' . $products->count());

        $deleted = 0;

        $this->output->progressStart($products->count());

        foreach ($products as $product) {
            $images = $this->getPhotoByEntityUseCase->execute(
                new PhotoByEntityData(
                    imageableId: $product->id,
                    modelType: self::MODEL_TYPE,
                    type: PhotoType::GALLERY,
                )
            );

            if ($images === null || $images === []) {
                $product->forceDelete();
                $deleted++;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('Удалено товаров парсера без изображений: ' . $deleted);

        return self::SUCCESS;
    }
}
