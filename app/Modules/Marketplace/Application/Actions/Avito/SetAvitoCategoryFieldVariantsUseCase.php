<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryFieldEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetAvitoCategoryFieldVariantsUseCase
{
    public function __construct(
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    /**
     * @param string[] $variants
     */
    public function execute(int $fieldId, array $variants, UserPermission $permission): AvitoCategoryFieldEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $field = $this->fieldRepository->getById($fieldId);

        // Варианты допустимы только для полей типа string и array.
        // У AvitoCategoryField нет признака is_html: поля-«общие параметры» (AvitoParameter)
        // с isHtml = true не отображаются в этой колонке.
        if (!in_array($field->type, ['string', 'array'], true)) {
            throw new \InvalidArgumentException('Варианты можно задавать только для полей типа string или array');
        }

        $field->allowedValues = $variants;

        return $this->fieldRepository->save($field);
    }
}
