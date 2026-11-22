<?php

namespace App\Modules\Shop\Domain\Schema;

readonly class ProductOfferSchema implements SchemaElement
{
    /**
     * @param array{name: string, url: string, brand?: array{name: string}} $seller
     */
    public function __construct(
        private float  $price,
        private string $priceCurrency = 'RUB',
        private string $availability = 'InStock',
        private array  $seller = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type' => 'Offer',
            'acceptedPaymentMethod' => [
                [
                    '@type' => 'PaymentMethod',
                    '@id'   => 'https://purl.org/goodrelations/v1#ByBankTransferInAdvance',
                ],
                [
                    '@type' => 'PaymentMethod',
                    '@id'   => 'https://purl.org/goodrelations/v1#ByInvoice',
                ],
                [
                    '@type' => 'PaymentMethod',
                    '@id'   => 'https://purl.org/goodrelations/v1#Cash',
                ],
            ],
            'deliveryLeadTime' => [
                '@type'    => 'QuantitativeValue',
                'unitCode' => 'DAY',
                'value'    => '14',
            ],
            'inventoryLevel' => [
                '@type'    => 'QuantitativeValue',
                'unitCode' => 'NMP',
                'value'    => '',
            ],
            'hasMerchantReturnPolicy' => [
                '@type'                 => 'MerchantReturnPolicy',
                'applicableCountry'     => 'RU',
                'returnPolicyCategory'  => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays'    => 60,
                'returnMethod'          => 'https://schema.org/ReturnByMail',
                'returnFees'            => 'https://schema.org/FreeReturn',
            ],
            'warranty' => [
                '@type'               => 'WarrantyPromise',
                'durationOfWarranty'  => [
                    '@type'    => 'QuantitativeValue',
                    'unitCode' => 'MON',
                    'value'    => '12',
                ],
            ],
            'itemCondition'             => 'https://schema.org/NewCondition',
            'availableDeliveryMethod'   => 'https://schema.org/OnSitePickup',
            'potentialAction'           => [
                '@type' => 'https://schema.org/BuyAction',
            ],
            'price'         => number_format($this->price, 2, '.', ''),
            'priceCurrency' => $this->priceCurrency,
            'availability'  => 'https://schema.org/' . $this->availability,
        ];

        if (!empty($this->seller)) {
            $data['seller'] = $this->seller;
        }

        return $data;
    }
}
