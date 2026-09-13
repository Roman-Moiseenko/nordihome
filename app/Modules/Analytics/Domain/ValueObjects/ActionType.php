<?php

namespace App\Modules\Analytics\Domain\ValueObjects;
use InvalidArgumentException;

final readonly class ActionType implements \Stringable
{
    // ============ Формы ============
    public const string FORM_SUBMIT = 'form_submit';

    // ============ Корзина ============
    public const string CART_ADD               = 'cart_add';
    public const string CART_REMOVE            = 'cart_remove';
    public const string CART_CLEAR             = 'cart_clear';
    public const string CART_QUANTITY_CHANGE   = 'cart_quantity_change';

    // ============ Оформление заказа ============
    public const string CHECKOUT_START         = 'checkout_start';
    public const string ORDER_PLACED           = 'order_placed';
    public const string ONE_CLICK_BUY          = 'one_click_buy';

    // ============ Аккаунт ============
    public const string REGISTER_ATTEMPT       = 'register_attempt';
    public const string REGISTER               = 'register';
    public const string LOGIN                  = 'login';

    // ============ Избранное ============
    public const string WISHLIST_ADD           = 'wishlist_add';
    public const string WISHLIST_REMOVE        = 'wishlist_remove';
    public const string WISHLIST_CLEAR         = 'wishlist_clear';

    // ============ Контакты ============
    public const string CONTACT_CLICK          = 'contact_click';

    // ============ Реклама / промо ============
    public const string BANNER_CLICK           = 'banner_click';

    // ============ Поиск ============
    public const string SEARCH_RESULT_CLICK    = 'search_result_click';

    private const array ALLOWED = [
        self::FORM_SUBMIT,
        self::CART_ADD,
        self::CART_REMOVE,
        self::CART_CLEAR,
        self::CART_QUANTITY_CHANGE,
        self::CHECKOUT_START,
        self::ORDER_PLACED,
        self::ONE_CLICK_BUY,
        self::REGISTER_ATTEMPT,
        self::REGISTER,
        self::LOGIN,
        self::WISHLIST_ADD,
        self::WISHLIST_REMOVE,
        self::WISHLIST_CLEAR,
        self::CONTACT_CLICK,
        self::BANNER_CLICK,
        self::SEARCH_RESULT_CLICK,
    ];

    private function __construct(public string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Invalid ActionType: {$value}");
        }
    }

    public static function from(string $value): self { return new self($value); }

    public static function formSubmit(): self           { return new self(self::FORM_SUBMIT); }
    public static function cartAdd(): self              { return new self(self::CART_ADD); }
    public static function cartRemove(): self           { return new self(self::CART_REMOVE); }
    public static function cartClear(): self            { return new self(self::CART_CLEAR); }
    public static function cartQuantityChange(): self   { return new self(self::CART_QUANTITY_CHANGE); }
    public static function checkoutStart(): self        { return new self(self::CHECKOUT_START); }
    public static function orderPlaced(): self          { return new self(self::ORDER_PLACED); }
    public static function oneClickBuy(): self          { return new self(self::ONE_CLICK_BUY); }
    public static function registerAttempt(): self      { return new self(self::REGISTER_ATTEMPT); }
    public static function register(): self             { return new self(self::REGISTER); }
    public static function login(): self                { return new self(self::LOGIN); }
    public static function wishlistAdd(): self          { return new self(self::WISHLIST_ADD); }
    public static function wishlistRemove(): self       { return new self(self::WISHLIST_REMOVE); }
    public static function wishlistClear(): self        { return new self(self::WISHLIST_CLEAR); }
    public static function contactClick(): self         { return new self(self::CONTACT_CLICK); }
    public static function bannerClick(): self          { return new self(self::BANNER_CLICK); }
    public static function searchResultClick(): self    { return new self(self::SEARCH_RESULT_CLICK); }

    public function equals(self $other): bool { return $this->value === $other->value; }
    public function __toString(): string      { return $this->value; }

    // ============ Группировки для фильтрации и отчётов ============

    public function isCart(): bool
    {
        return in_array($this->value, [
            self::CART_ADD, self::CART_REMOVE, self::CART_CLEAR, self::CART_QUANTITY_CHANGE,
        ], true);
    }

    public function isWishlist(): bool
    {
        return in_array($this->value, [
            self::WISHLIST_ADD, self::WISHLIST_REMOVE, self::WISHLIST_CLEAR,
        ], true);
    }

    public function isAuth(): bool
    {
        return in_array($this->value, [self::REGISTER_ATTEMPT, self::REGISTER, self::LOGIN], true);
    }

    public function isCheckout(): bool
    {
        return in_array($this->value, [
            self::CHECKOUT_START, self::ORDER_PLACED, self::ONE_CLICK_BUY,
        ], true);
    }

    public function isConversion(): bool
    {
        return in_array($this->value, [self::ORDER_PLACED, self::ONE_CLICK_BUY], true);
    }
}
