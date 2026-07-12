<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

class ImageInfoData
{

    public function __construct(

        public string $src,
        public string $alt,
        public string $title = '',
        public string $description = ''
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            src: $data['src'],
            alt: $data['alt'],
            title: $data['title'],
            description: $data['description']
        );
    }
}
