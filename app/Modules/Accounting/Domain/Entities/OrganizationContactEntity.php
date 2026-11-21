<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;

final class OrganizationContactEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public FullName $fullName {
        get => $this->fullName;
        set => $this->fullName = $value;
    }

    public string $post = '' {
        get => $this->post;
        set => $this->post = $value;
    }

    public PhoneNumber $phone {
        get => $this->phone;
        set => $this->phone = $value;
    }

    public ?Email $email = null {
        get => $this->email;
        set => $this->email = $value;
    }

    public function __construct(FullName $fullName)
    {
        $this->fullName = $fullName;
        $this->phone = new PhoneNumber(null);
    }
}
