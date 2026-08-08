<?php

namespace App\Modules\Auth\Application\DTOs\Client;

use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\Gender;
use App\Modules\Auth\Domain\ValueObjects\PersonalDataConsent;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;

class ClientInfoWebData
{
    public function __construct(
        public int $id,
        /** @var int[] */
        public FullName $fullName,
        public Email $email,
        public int $userId,
        public Email $loginEmail,
        public PriceType $priceType,
        public float $discount,
        public PhoneNumber $phone,
        public ?PersonalDataConsent $consent,
        public Address $address,
        public ?Gender $gender,
        public bool $isPickup,
    )
    {

    }


}
