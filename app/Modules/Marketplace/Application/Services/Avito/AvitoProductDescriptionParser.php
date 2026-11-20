<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Services\Avito;

/**
 * Разбор поля «Описание объявления» из выгрузки Авито.
 *
 * Правила:
 * 1. Всё до <strong>ОПИСАНИЕ ТОВАРА:</strong> (или «ХАРАКТЕРИСТИКИ ТОВАРА»,
 *    возможны неполные/иные написания) вырезается вместе с тегом <strong>.
 * 2. Следующий текст до строки из длинных тире (обычно 10 и более) кладётся
 *    в description.
 * 3. В остатке ищется артикул «Артикул: 106.275.53» — возвращается первый код.
 */
class AvitoProductDescriptionParser
{
    /**
     * @return array{description: ?string, article: ?string}
     */
    public function parse(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return ['description' => null, 'article' => null];
        }

        $text = $this->cutHeader($raw);
        $text = trim($text);

        if ($text === '') {
            return ['description' => null, 'article' => null];
        }

        $description = $text;
        $article = null;

        // Обрезаем по первой строке из длинных тире (10 и более подряд).
        if (preg_match('/-{10,}/u', $text, $m, PREG_OFFSET_CAPTURE)) {
            $description = trim(substr($text, 0, $m[0][1]));
            $tail = substr($text, $m[0][1] + strlen($m[0][0]));
            $article = $this->extractArticle($tail);
        } else {
            // Тире нет — артикул ищем во всём тексте.
            $article = $this->extractArticle($text);
        }

        return [
            'description' => $description !== '' ? $description : null,
            'article' => $article,
        ];
    }

    private function cutHeader(string $text): string
    {
        // <strong>...ОПИСАНИЕ ТОВАРА:...</strong> (или «ХАРАКТЕРИСТИКИ ТОВАРА»).
        $pattern = '~<strong[^>]*>[^<]*(?:описание товара|характеристи[кист]* товара)[^<]*</strong>~iu';

        if (preg_match($pattern, $text, $m, PREG_OFFSET_CAPTURE)) {
            return substr($text, $m[0][1] + strlen($m[0][0]));
        }

        // Маркер без тега <strong>.
        $markers = ['описание товара', 'характеристики товара', 'характеристки товара'];
        $lower = mb_strtolower($text);

        foreach ($markers as $marker) {
            $pos = mb_strpos($lower, $marker);
            if ($pos === false) {
                continue;
            }

            $after = mb_substr($text, $pos + mb_strlen($marker));
            $colon = mb_strpos($after, ':');
            if ($colon !== false && $colon <= 3) {
                $after = mb_substr($after, $colon + 1);
            }

            return ltrim($after);
        }

        return $text;
    }

    private function extractArticle(string $text): ?string
    {
        if (preg_match('/Артикул\s*:\s*([^\s<]+)/iu', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }
}
