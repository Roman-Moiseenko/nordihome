<?php
declare(strict_types=1);

if (!function_exists('price')) {
    function price($value): string
    {
        if (empty($value) || !is_numeric($value)) return '0 ₽';
        return number_format($value, 0, ',', ' ') . ' ₽';
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
