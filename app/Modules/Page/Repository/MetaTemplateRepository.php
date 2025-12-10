<?php

namespace App\Modules\Page\Repository;

use App\Modules\Base\Entity\Meta;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\MetaTemplate;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Http\Request;

class MetaTemplateRepository
{

    private Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getIndex(Request $request)
    {
        return MetaTemplate::get()->map(fn(MetaTemplate $meta) => $this->MetaToArray($meta))->toArray();
    }

    private function MetaToArray(MetaTemplate $meta): array
    {
        return array_merge($meta->toArray(), [
            'name' => MetaTemplate::TEMPLATES[$meta->class],
            'variables' => $this->GetVariables($meta->class),
        ]);
    }

    private function GetVariables(string $class): array
    {
        if ($class == Product::class) {
            return [
                '{name}',
                '{code}',
                '{category}',
                '{price}',
            ];
        }
        if ($class == ProductParser::class) {
            return [
                '{name}',
                '{code}',
           //     '{category}',
                '{price}',
            ];
        }
        if ($class == Category::class) {
            return [
                '{name}',
                '{description}',
                '{title}'
            ];
        }
        if ($class == CategoryParser::class) {
            return [
                '{name}',
             //   '{description}',
              //  '{title}'
            ];
        }
        if ($class == Page::class) {
            return [
                '{name}',
                '{title}',
                '{description}',
                '{fragment}',
            ];
        }
        if ($class == Post::class) {
            return [
                '{name}',
                '{title}',
                '{description}',
                '{fragment}',
            ];
        }
        if ($class == PostCategory::class) {
            return [
                '{name}',
                '{title}',
                '{description}',
                ];
        }
        if ($class == Group::class) {
            return [
                '{name}',
                '{description}',
            ];
        }
        if ($class == Promotion::class) {
            return [
                '{name}',
                '{title}',
                '{description}',
            ];
        }
        return [];
    }


    private function GetValue($object, string $var): string
    {
        if ($object instanceof Product) {
            return match ($var) {
                '{name}' => $object->name,
                '{code}' => $object->code,
                '{category}' => $object->category->name,
                '{price}' => price($object->getPrice()),
            };
        }

        if ($object instanceof Category) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
                '{title}' => $object->title,
            };
        }
        if ($object instanceof ProductParser) {
            return match ($var) {
                '{name}' => $object->product->name,
                '{code}' => $object->product->code,
                //'{category}' => $object->category->name,
                //TODO parser_coefficient - или из Валюты или обновлять сразу два поля
                '{price}' => price($object->price_sell * $this->settings->parser->parser_coefficient),
            };
        }

        if ($object instanceof CategoryParser) {
            return match ($var) {
                '{name}' => $object->name,
               // '{description}' => $object->description,
               // '{title}' => $object->title,
            };
        }
        if ($object instanceof Page) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
                '{title}' => $object->title,
                '{fragment}' => $object->getFragment(),
            };
        }
        if ($object instanceof Post) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
                '{title}' => $object->title,
                '{fragment}' => $object->getFragment(),
            };
        }
        if ($object instanceof PostCategory) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
                '{title}' => $object->title,
            };
        }
        if ($object instanceof Group) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
            };
        }
        if ($object instanceof Promotion) {
            return match ($var) {
                '{name}' => $object->name,
                '{description}' => $object->description,
                '{title}' => $object->title,
            };
        }

        return '';

    }

    /**
     * Подстановка переменных в meta
     */
    public function seo($object, Meta $meta = null): Meta
    {
        if (is_null($meta)) $meta = new Meta();
        /** @var MetaTemplate $metaTemplate */
        $metaTemplate = MetaTemplate::where('class', get_class($object))->first();
        if (empty($meta->title)) $meta->title = $this->renderMeta($object, $metaTemplate->template_title);
        if (empty($meta->description)) $meta->description = $this->renderMeta($object, $metaTemplate->template_description);
        return $meta;
    }

    /**
     * Ф-ция обратного вызова, для seo() "Подстановка переменных в meta"
     */
    public function seoFn(): callable
    {
        return function ($object, Meta $meta) {
            return $this->seo($object, $meta);
        };
    }

    private function renderMeta($object, $text): string
    {
        if (empty($text)) return '';

        $pattern = '/\{(.+?)}/';
        preg_match_all($pattern, $text, $matches);
        $replaces = $matches[0];
        foreach ($replaces as $replace) {
            $text = str_replace(
                $replace,
                $this->GetValue($object, $replace),
                $text);
        }
        return $text;
    }


}
