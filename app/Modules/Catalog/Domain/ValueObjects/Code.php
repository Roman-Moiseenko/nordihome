<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;

final class Code
{
    private string $code;
    private string $codeSearch;

    public function __construct(string $code)
    {
        if (empty($code)) {
            throw new InvalidArgumentException('Code не может быть пустым');
        }

        $this->code = $code;
        $this->codeSearch = str_replace(['-', ',', '.', '_', ':'], '', $code);
    }

    /**
     * Создать Code из двух значений (для гидрации из модели)
     */
    public static function fromDatabase(string $code, string $codeSearch): self
    {
        $self = new self($code);
        $self->codeSearch = $codeSearch;
        return $self;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCodeSearch(): string
    {
        return $this->codeSearch;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
