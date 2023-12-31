<?php
declare(strict_types=1);

if (!function_exists('price')) {
    function price($value): string
    {
        if (empty($value) || !is_numeric($value)) return '';
        return number_format($value, 0, ',', ' ') . ' ₽';
    }
}
