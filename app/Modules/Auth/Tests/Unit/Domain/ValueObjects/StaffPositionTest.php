<?php

namespace App\Modules\Auth\Tests\Unit\Domain\ValueObjects;

use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StaffPositionTest extends TestCase
{
    #[Test]
    public function it_creates_valid_position(): void
    {
        $position = new StaffPosition('driver');
        $this->assertSame('driver', $position->getValue());
        $this->assertTrue($position->isDriver());
    }

    #[Test]
    public function it_normalizes_case(): void
    {
        $position = new StaffPosition('SUPERVISOR');
        $this->assertSame('supervisor', $position->getValue());
        $this->assertTrue($position->isSupervisor());
    }

    #[Test]
    public function it_throws_on_invalid_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaffPosition('developer');
    }

    #[Test]
    public function it_exposes_static_lists(): void
    {
        $this->assertContains(StaffPosition::SUPERVISOR, StaffPosition::allowed());
        $this->assertContains(StaffPosition::CUSTOMER_MANAGER, StaffPosition::managers());
        $this->assertContains(StaffPosition::DRIVER, StaffPosition::workers());
        $this->assertSame('Руководитель', StaffPosition::positions()[StaffPosition::SUPERVISOR]);
    }

    #[Test]
    public function it_provides_static_factories(): void
    {
        $this->assertSame('supervisor', StaffPosition::supervisor()->getValue());
        $this->assertSame('driver', StaffPosition::driver()->getValue());
        $this->assertSame('assembler', StaffPosition::assembler()->getValue());
        $this->assertSame('administrator', StaffPosition::administrator()->getValue());
    }

    #[Test]
    public function it_compares_equal_positions(): void
    {
        $a = new StaffPosition('logist');
        $b = new StaffPosition('logist');
        $c = new StaffPosition('loader');
        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
