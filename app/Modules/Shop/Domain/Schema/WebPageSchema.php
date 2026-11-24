<?php

namespace App\Modules\Shop\Domain\Schema;

readonly class WebPageSchema implements SchemaElement
{
    /**
     * @param array{@type: string, target?: array{@type: string, urlTemplate: string}, 'query-input'?: string} $potentialAction
     * @param array{@type: string, url: string, width: int, height: int, caption: string}|null $primaryImageOfPage
     */
    public function __construct(
        private string $name,
        private string $description,
        private string $url,
        private string $inLanguage = 'ru-RU',
        private ?string $datePublished = null,
        private ?string $dateModified = null,
        private array $potentialAction = [],
        private ?array $primaryImageOfPage = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type'       => 'WebPage',
            'name'        => $this->name,
            'description' => $this->description,
            'url'         => $this->url,
            'inLanguage'  => $this->inLanguage,
        ];

        if ($this->datePublished !== null) {
            $data['datePublished'] = $this->datePublished;
        }

        if ($this->dateModified !== null) {
            $data['dateModified'] = $this->dateModified;
        }

        if (!empty($this->potentialAction)) {
            $data['potentialAction'] = $this->potentialAction;
        }

        if ($this->primaryImageOfPage !== null) {
            $data['primaryImageOfPage'] = $this->primaryImageOfPage;
        }

        return $data;
    }
}
