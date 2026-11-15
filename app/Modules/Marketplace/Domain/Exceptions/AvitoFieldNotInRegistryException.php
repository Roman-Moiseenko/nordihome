<?php

namespace App\Modules\Marketplace\Domain\Exceptions;

use RuntimeException;

/**
 * Поле шаблона отсутствует в реестре параметров.
 */
final class AvitoFieldNotInRegistryException extends RuntimeException
{
    /**
     * @param string[] $fields
     */
    public function __construct(
        private readonly array $fields,
    ) {
        parent::__construct(
            'Нет данных в реестре для полей: ' . implode(', ', $fields)
        );
    }

    /**
     * @return string[]
     */
    public function fields(): array
    {
        return $this->fields;
    }
}
