<?php

namespace App\Modules\Shop\Domain\Schema;

class FAQSchema implements SchemaElement
{
    /** @param array{question:string, answer:string}[] $items */
    public function __construct(private array $items) {}

    public function toArray(): array
    {
        $mainEntity = [];
        foreach ($this->items as $item) {
            $mainEntity[] = [
                '@type'          => 'Question',
                'name'           => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['answer'],
                ],
            ];
        }

        return [
            '@type'       => 'FAQPage',
            'mainEntity'  => $mainEntity,
        ];
    }
}