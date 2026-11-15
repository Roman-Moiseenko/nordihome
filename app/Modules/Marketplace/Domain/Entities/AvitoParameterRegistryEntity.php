<?php

namespace App\Modules\Marketplace\Domain\Entities;

/**
 * Запись реестра параметров Авито.
 *
 * Хранит сопоставление английского названия поля шаблона (nameEn) с его видом
 * (kind) и, при необходимости, именем поля сущности AvitoProductEntity (field).
 * Русское название (nameRu) используется только для вывода в админке.
 *
 * Варианты значений реестр НЕ хранит — они извлекаются из шаблона при загрузке
 * категорий.
 */
final class AvitoParameterRegistryEntity
{
    public const KIND_PRODUCT = 'product';
    public const KIND_COMMON = 'common';
    public const KIND_SKIP = 'skip';
    public const KIND_CATEGORY = 'category';

    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $nameEn {
        get => $this->nameEn;
        set => $this->nameEn = $value;
    }

    public ?string $nameRu = null {
        get => $this->nameRu;
        set => $this->nameRu = $value;
    }

    public ?string $kind = null {
        get => $this->kind;
        set => $this->kind = $value;
    }

    public ?string $field = null {
        get => $this->field;
        set => $this->field = $value;
    }

    public ?string $type = null {
        get => $this->type;
        set => $this->type = $value;
    }

    public bool $isRequired = false {
        get => $this->isRequired;
        set => $this->isRequired = $value;
    }

    public function __construct(
        string $nameEn,
        ?string $nameRu = null,
        ?string $kind = null,
        ?string $field = null,
        ?string $type = null,
        bool $isRequired = false,
    ) {
        $this->nameEn = $nameEn;
        $this->nameRu = $nameRu;
        $this->kind = $kind;
        $this->field = $field;
        $this->type = $type;
        $this->isRequired = $isRequired;
    }

    public static function kinds(): array
    {
        return [
            self::KIND_PRODUCT,
            self::KIND_COMMON,
            self::KIND_SKIP,
            self::KIND_CATEGORY,
        ];
    }
}
