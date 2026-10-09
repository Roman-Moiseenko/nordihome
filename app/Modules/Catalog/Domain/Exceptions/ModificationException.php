<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use DomainException;

/**
 * Базовое исключение домена модификаций.
 * Наследники ловятся снаружи одним catch — удобно на границе HTTP.
 */
class ModificationException extends DomainException
{
}
