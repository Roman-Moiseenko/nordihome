<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

class ImageInfoData
{

    public function __construct(
        public string $full,
        public string $src,
        public string $alt,
        public string $title = '',
        public string $description = '',
        public string $mini = '',

    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            full: $data['full'] ?? '',
            src: $data['src'],
            alt: $data['alt'],
            title: $data['title'],
            description: $data['description'],
            mini: $data['mini'] ?? '',
        );
    }
}
