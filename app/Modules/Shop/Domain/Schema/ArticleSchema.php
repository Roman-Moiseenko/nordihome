<?php

namespace App\Modules\Shop\Domain\Schema;

readonly class ArticleSchema implements SchemaElement
{
    /**
     * @param string[] $image
     * @param array{@type: string, name: string, url?: string} $author
     * @param array{@type: string, name: string, logo?: array{@type: string, url: string}} $publisher
     */
    public function __construct(
        private string $headline,
        private string $description = '',
        private array $image = [],
        private ?string $datePublished = null,
        private ?string $dateModified = null,
        private array $author = [],
        private array $publisher = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type'    => 'Article',
            'headline' => $this->headline,
        ];

        if ($this->description !== '') {
            $data['description'] = $this->description;
        }

        if (!empty($this->image)) {
            $data['image'] = $this->image;
        }

        if ($this->datePublished !== null && $this->datePublished !== '') {
            $data['datePublished'] = $this->datePublished;
        }

        if ($this->dateModified !== null && $this->dateModified !== '') {
            $data['dateModified'] = $this->dateModified;
        }

        if (!empty($this->author)) {
            $data['author'] = $this->author;
        }

        if (!empty($this->publisher)) {
            $data['publisher'] = $this->publisher;
        }

        return $data;
    }
}
