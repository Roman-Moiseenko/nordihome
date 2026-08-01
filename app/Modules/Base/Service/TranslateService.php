<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

use App\Modules\Base\Entity\Translate;

class TranslateService
{
    public function translate(string $foreign, string $lang = 'pl'): string
    {
        if (strlen($foreign) < 64) {
            if ($translate = Translate::where('foreign', $foreign)->first()) return $translate->value;
        }

            try {
                $value = YandexTranslate::translate($foreign);
            } catch (\Throwable) {
                try {
                    $value = GoogleTranslateForFree::translate($lang, 'ru', $foreign);
                } catch (\Throwable) {
                    return $foreign;
                }
            }
            if (strlen($foreign) < 64) Translate::register($foreign, $value);
            return $value;

        return '';
    }
}
