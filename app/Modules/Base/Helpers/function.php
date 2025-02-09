<?php
declare(strict_types=1);

if (!function_exists('price')) {
    function price($value): string
    {
        if (empty($value) || !is_numeric($value)) return '0 ₽';
        return number_format($value, 0, ',', ' ') . ' ₽';
    }
}

if (!function_exists('count_product')) {
    function count_product(int $count): string
    {
        $basis = 'товар';
        $ending_a = 'а';
        $ending_ov = 'ов';
        $mod_10 = $count % 10;
        $mod_100 = $count % 100;

        if ($count >= 11 && $count <= 19) return $count . ' ' . $basis . $ending_ov;
        if ($mod_100 >= 11 && $mod_100 <= 19) return $count  . ' ' . $basis . $ending_ov;
        if ($mod_10 == 1) return $count . ' ' . $basis;
        if (in_array($mod_10, [2,3,4])) return $count . ' ' . $basis . $ending_a;
        return $count . ' ' . $basis . $ending_ov;
    }
}

if (!function_exists('modules')) {
    /**
     * Список всех Модулей в приложении
     * @return array
     */
    function modules(): array
    {
        $modules_folder = app_path('Modules');

        return array_values(
            array_filter(
                scandir($modules_folder),
                function ($item) use ($modules_folder) {
                    return is_dir($modules_folder . DIRECTORY_SEPARATOR . $item) && !in_array($item, ['.', '..']);
                }
            )
        );
    }
}


if (!function_exists('modules_callback')){
    /**
     *
     * @param string $file - имя файла в папке Модуля
     * @param callable $callback - функция fn($filePath, $module) обработки для каждого файла $filePath из модуля $module
     * @return void
     */
    function modules_callback(string $file, callable $callback) {
        foreach (modules() as $module) {
            $filePath = app_path('Modules') . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filePath)) {
                $callback($filePath, $module);
            }
        }

    }
}

if (!function_exists('phone')) {
    function phone($value): string
    {
        if (empty($value) || !is_numeric($value)) return '';
        return mb_substr($value, 0, 1) . ' (' . mb_substr($value, 1, 3) . ') ' .
            mb_substr($value, 4, 3) . '-' . mb_substr($value, 7, 2) . '-' . mb_substr($value, 9, 2);
    }
}

if (!function_exists('phoneToDB')) {
    function phoneToDB(Stringable|string $phone): string
    {
        if ($phone instanceof Stringable) $phone = $phone->trim()->value();
        return preg_replace("/[^0-9]/", "", $phone);
    }
}

if (!function_exists('shortname')) {
    function shortname($value): string
    {
        if (empty($value)) return '';
        return ($value['surname'] ?? '') . ' ' .
            mb_substr($value['firstname'], 0, 1) . '.' .
            (!empty($value['secondname']) ? mb_substr($value['secondname'], 0, 1) . '.' : '') ;
    }
}

if (!function_exists('fullname')) {
    function fullname($value): string
    {
        if (empty($value)) return '';


        return ($value['surname'] ?? '') . ' ' .
            $value['firstname'] . ' ' .
            (!empty($value['secondname']) ? ' ' . $value['secondname'] : '') ;
    }
}
if (!function_exists('array_select')) {
    /**
     * Приведение ассоциативного массива для фильтра в Vue
     * @param array $array
     * @return array
     */
    function array_select(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[] = [
                'value' => $key,
                'label' => $value,
            ];
        }
        return $result;
    }
}
