<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use App\Modules\Accounting\Domain\ValueObjects\Geo;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;

final class OrganizationEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $fullName {
        get => $this->fullName;
        set => $this->fullName = $value;
    }

    public string $shortName {
        get => $this->shortName;
        set => $this->shortName = $value;
    }

    public string $inn {
        get => $this->inn;
        set => $this->inn = $value;
    }

    public string $kpp = '' {
        get => $this->kpp;
        set => $this->kpp = $value;
    }

    public string $ogrn = '' {
        get => $this->ogrn;
        set => $this->ogrn = $value;
    }

    public bool $foreign = false {
        get => $this->foreign;
        set => $this->foreign = $value;
    }

    public string $bik = '' {
        get => $this->bik;
        set => $this->bik = $value;
    }

    public string $bankName = '' {
        get => $this->bankName;
        set => $this->bankName = $value;
    }

    public string $corrAccount = '' {
        get => $this->corrAccount;
        set => $this->corrAccount = $value;
    }

    public string $payAccount = '' {
        get => $this->payAccount;
        set => $this->payAccount = $value;
    }

    public ?int $vatId = null {
        get => $this->vatId;
        set => $this->vatId = $value;
    }

    public ?Email $email = null {
        get => $this->email;
        set => $this->email = $value;
    }

    public PhoneNumber $phone {
        get => $this->phone;
        set => $this->phone = $value;
    }

    public string $post = '' {
        get => $this->post;
        set => $this->post = $value;
    }

    public FullName $chief {
        get => $this->chief;
        set => $this->chief = $value;
    }

    public bool $active = true {
        get => $this->active;
        set => $this->active = $value;
    }

    public string $legalAddress = '' {
        get => $this->legalAddress;
        set => $this->legalAddress = $value;
    }

    public Address $actualAddress {
        get => $this->actualAddress;
        set => $this->actualAddress = $value;
    }

    public Geo $geo {
        get => $this->geo;
        set => $this->geo = $value;
    }

    /** @var OrganizationContactEntity[] */
    public array $contacts = [] {
        get => $this->contacts;
        set => $this->contacts = $value;
    }

    public function __construct(
        string $fullName,
        string $shortName,
        string $inn,
    ) {
        $this->fullName = $fullName;
        $this->shortName = $shortName;
        $this->inn = $inn;
        $this->chief = new FullName('');
        $this->phone = new PhoneNumber(null);
        $this->actualAddress = new Address();
        $this->geo = new Geo();
    }

    public function isForeign(): bool
    {
        return $this->foreign;
    }
}
