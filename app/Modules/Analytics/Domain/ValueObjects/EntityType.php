<?php

namespace App\Modules\Analytics\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class EntityType implements \Stringable
{
    public const PRODUCT = 'product';
    public const PARSER_PRODUCT = 'parser.product';
    public const CATEGORY = 'category';
    public const ROOM = 'room';
    public const PARSER_CATEGORY = 'parser.category';
    public const PROMOTION = 'promotion';
    public const POST = 'post';
    public const PAGE = 'page';
    public const BANNER = 'banner';
    public const FORM = 'form';
    public const CART = 'cart';
    public const ORDER = 'order';
    public const USER = 'user';
    public const SEARCH = 'search';

    private const ALLOWED = [
        self::PARSER_PRODUCT, self::PARSER_CATEGORY, self::ROOM,
        self::PRODUCT, self::CATEGORY, self::PROMOTION, self::POST,
        self::PAGE, self::BANNER, self::FORM, self::CART,
        self::ORDER, self::USER, self::SEARCH,
    ];

    private function __construct(public string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Invalid EntityType: {$value}");
        }
    }

    public static function from(string $value): self
    {
        return new self($value);
    }


    public static function parserProduct(): self
    {
        return new self(self::PARSER_PRODUCT);
    }

    public static function parserCategory(): self
    {
        return new self(self::PARSER_CATEGORY);
    }

    public static function room(): self
    {
        return new self(self::ROOM);
    }

    public static function product(): self
    {
        return new self(self::PRODUCT);
    }

    public static function category(): self
    {
        return new self(self::CATEGORY);
    }

    public static function promotion(): self
    {
        return new self(self::PROMOTION);
    }

    public static function post(): self
    {
        return new self(self::POST);
    }

    public static function page(): self
    {
        return new self(self::PAGE);
    }

    public static function banner(): self
    {
        return new self(self::BANNER);
    }

    public static function form(): self
    {
        return new self(self::FORM);
    }

    public static function cart(): self
    {
        return new self(self::CART);
    }

    public static function order(): self
    {
        return new self(self::ORDER);
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function search(): self
    {
        return new self(self::SEARCH);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
