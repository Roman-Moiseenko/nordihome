<?php
declare(strict_types=1);

namespace App\Modules\Shop;

class Helper
{
    public static function _countProd(int $count): string
    {
        $basis = 'товар';
        $ending_a = 'а';
        $ending_ov = 'ов';
        $mod_10 = $count % 10;
        $mod_100 = $count % 100;
        if ($count >= 11 && $count <= 19) return self::_formatCountProduct($count, $basis . $ending_ov);
        if ($mod_100 >= 11 && $mod_100 <= 19) return self::_formatCountProduct($count, $basis . $ending_ov);
        if ($mod_10 == 1) return self::_formatCountProduct($count, $basis);
        if (in_array($mod_10, [2,3,4])) return self::_formatCountProduct($count, $basis . $ending_a);
        return self::_formatCountProduct($count, $basis . $ending_ov);
    }

    private static function _formatCountProduct($int, $str): string
    {
        return $int . ' ' . $str;
    }
}
