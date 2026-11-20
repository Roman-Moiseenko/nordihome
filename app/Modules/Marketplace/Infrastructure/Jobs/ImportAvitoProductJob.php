<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Infrastructure\Jobs;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductImportCreateData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductValueRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создание одного объявления Авито и заполнение его параметров категории.
 *
 * Если параметр предусматривает варианты, и загруженного варианта ещё нет
 * в allowed_values — вариант добавляется в параметр.
 */
class ImportAvitoProductJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public array $payload,
    ) {}

    public function handle(
        AvitoProductRepositoryInterface $productRepository,
        AvitoProductValueRepositoryInterface $valueRepository,
        AvitoCategoryFieldRepositoryInterface $fieldRepository,
        PriceRepositoryInterface $priceRepository,
    ): void {
        $dto = AvitoProductImportCreateData::from($this->payload);

        $entity = new AvitoProductEntity(
            name: $dto->name,
            productId: $dto->productId,
            avitoCategoryId: $dto->categoryId,
        );

        $entity->description = $dto->description;
        $entity->sellPrice = $dto->sellPrice;
        $entity->avitoImages = $dto->avitoImages;
        $entity->tnVed = $dto->tnVed;
        $entity->okpd2 = $dto->okpd2;
        $entity->country = $dto->country ?: 'Россия';
        $entity->delivery = $dto->delivery;
        $entity->inStock = true;
        $entity->active = true;
        $entity->sale = false;

        if ($dto->productId !== null) {
            $retail = (float) ($priceRepository->findCurrentPrices($dto->productId)[PriceType::RETAIL] ?? 0);
            $entity->basePrice = (int) round($retail);
        }

        $saved = $productRepository->save($entity);

        foreach ($dto->parameters as $parameter) {
            $fieldId = (int) $parameter['avito_category_field_id'];
            $value = $parameter['value'] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $valueRepository->save(new AvitoProductValueEntity(
                avitoProductId: $saved->id,
                avitoCategoryFieldId: $fieldId,
                value: $value,
            ));

            $this->ensureVariants($fieldRepository, $fieldId, $value);
        }
    }

    private function ensureVariants(
        AvitoCategoryFieldRepositoryInterface $fieldRepository,
        int $fieldId,
        string $value,
    ): void {
        $field = $fieldRepository->getById($fieldId);

        if ($field->allowedValues === null || $field->allowedValues === []) {
            return;
        }

        $incoming = $this->extractValues($field->type, $value);
        $missing = array_values(array_filter(
            $incoming,
            fn(string $variant) => !in_array($variant, $field->allowedValues, true),
        ));

        if ($missing === []) {
            return;
        }

        $field->allowedValues = array_values(array_unique(array_merge($field->allowedValues, $missing)));
        $fieldRepository->save($field);
    }

    /**
     * @return string[]
     */
    private function extractValues(?string $type, string $value): array
    {
        if ($type === 'array') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? array_values(array_map('strval', $decoded)) : [];
        }

        return [$value];
    }
}
