<?php

namespace App\Modules\Shop\Domain\Schema;

class OrganizationSchema implements SchemaElement
{
    /**
     * @param string[] $phones
     * @param array{postalCode?: string, addressLocality?: string, streetAddress?: string} $address
     * @param string[] $sameAs
     */
    public function __construct(
        private string $name,
        private string $url,
        private string $logoUrl = '',
        private int $logoWidth = 0,
        private int $logoHeight = 0,
        private string $logoCaption = '',
        private array $phones = [],
        private array $address = [],
        private array $sameAs = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type' => 'Organization',
            'name' => $this->name,
            'url' => $this->url,
        ];

        if ($this->logoUrl !== '') {
            $logo = [
                '@type' => 'ImageObject',
                'url' => $this->logoUrl,
                'contentUrl' => $this->logoUrl,
            ];

            if ($this->logoWidth > 0) $logo['width'] = $this->logoWidth;
            if ($this->logoHeight > 0) $logo['height'] = $this->logoHeight;
            if ($this->logoCaption !== '') $logo['caption'] = $this->logoCaption;

            $data['logo'] = $logo;
        }

        if (!empty($this->phones)) {
            $data['contactPoint'] = [
                '@type' => 'ContactPoint',
                'contactOption' => 'TollFree',
                'contactType' => 'Customer Service',
                'telephone' => array_values($this->phones),
            ];
        }

        if (!empty($this->address)) {
            $address = ['@type' => 'PostalAddress'];

            if (!empty($this->address['postalCode'])) $address['postalCode'] = $this->address['postalCode'];
            if (!empty($this->address['addressLocality'])) $address['addressLocality'] = $this->address['addressLocality'];
            if (!empty($this->address['streetAddress'])) $address['streetAddress'] = $this->address['streetAddress'];

            $data['address'] = $address;
        }

        if (!empty($this->sameAs)) {
            $data['sameAs'] = array_values($this->sameAs);
        }

        return $data;
    }
}
