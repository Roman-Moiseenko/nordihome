<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

class SeoData
{
    public function __construct(
        // Базовые
        public string  $title = '',
        public string  $description = '',
        public ?string $canonical = null,

        // OpenGraph
        public string  $ogType = 'website',              // website | product | article
        public string  $ogLocale = 'ru_RU',
        public ?string $ogSiteName = null,
        public ?string $ogUrl = null,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,

        /** @var OgImage[] */
        public array $ogImages = [],

        // Товар
        public ?float  $productPrice = null,
        public string  $productCurrency = 'RUB',
        public ?string $productAvailability = null,      // instock | outofstock | preorder
        public ?string $productRetailerId = null,
        public string  $productCondition = 'new',

        // Статья
        public ?string $articlePublishedTime = null,
        public ?string $articleModifiedTime = null,
        public ?string $articleAuthor = null,
        public ?string $articleSection = null,    )
    {

    }


    // ============ Хелперы для удобства наполнения ============

    public function withCanonical(string $url): self
    {
        return $this->copyWith(canonical: $url, ogUrl: $url);
    }

    public function asProduct(): self
    {
        return $this->copyWith(ogType: 'product');
    }

    public function asArticle(): self
    {
        return $this->copyWith(ogType: 'article');
    }

    public function withOgImage(OgImage $image): self
    {
        $images = $this->ogImages;
        $images[] = $image;
        return $this->copyWith(ogImages: $images);
    }

    private function copyWith(...$overrides): self
    {
        $current = get_object_vars($this);
        return new self(...array_merge($current, $overrides));
    }

    public function addImage(OgImage $image): void
    {
        $this->ogImages[] = $image;
    }
}
