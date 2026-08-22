<?php

namespace App\Modules\Auth\Tests\Unit\Domain\ValueObjects;

use App\Modules\Auth\Domain\ValueObjects\ProfileType;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Auth\Infrastructure\Models\Staff;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProfileTypeTest extends TestCase
{
    #[Test]
    public function it_has_expected_cases(): void
    {
        $this->assertSame('staff', ProfileType::STAFF->value);
        $this->assertSame('freelance', ProfileType::FREELANCE->value);
        $this->assertSame('client', ProfileType::CLIENT->value);
    }

    #[Test]
    public function it_maps_to_model_class(): void
    {
        $this->assertSame(Staff::class, ProfileType::STAFF->getModelClass());
        $this->assertSame(Client::class, ProfileType::CLIENT->getModelClass());
    }

    #[Test]
    public function it_maps_from_model_class(): void
    {
        $this->assertSame(ProfileType::STAFF, ProfileType::fromModelClass(Staff::class));
        $this->assertSame(ProfileType::CLIENT, ProfileType::fromModelClass(Client::class));
        $this->assertNull(ProfileType::fromModelClass(null));
    }

    #[Test]
    public function it_throws_on_unknown_model_class(): void
    {
        $this->expectException(\DomainException::class);
        ProfileType::fromModelClass(\stdClass::class);
    }
}
