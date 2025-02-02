<?php
declare(strict_types=1);

namespace App\Modules\Base\Service;

use App\Modules\Base\Entity\Translate;

class TranslateService
{
    public function translate(string $foreign, string $lang = 'pl'): string
    {
        if (is_null($value = Translate::where('foreign', $foreign)->first())) {
            $value = GoogleTranslateForFree::translate($lang,'ru', $foreign);
            Translate::register($foreign, $value);
        }
        return $value;
    }
}
