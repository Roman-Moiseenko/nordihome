<?php

namespace App\Modules\Auth\Tests\Unit\Domain\ValueObjects;

use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StaffPositionsTest extends TestCase
{
    #[Test]
    public function it_creates_from_string_array(): void
    {
        $positions = new StaffPositions([StaffPosition::DRIVER, StaffPosition::ASSEMBLER]);
        $this->assertSame([StaffPosition::DRIVER, StaffPosition::ASSEMBLER], $positions->toArrayOfStrings());
    }

    #[Test]
    public function it_removes_duplicates(): void
    {
        $positions = new StaffPositions([StaffPosition::DRIVER, StaffPosition::DRIVER]);
        $this->assertSame([StaffPosition::DRIVER], $positions->toArrayOfStrings());
    }

    #[Test]
    public function it_throws_on_empty_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaffPositions([]);
    }

    #[Test]
    public function it_checks_contains(): void
    {
        $positions = new StaffPositions([StaffPosition::DRIVER]);
        $this->assertTrue($positions->contains(new StaffPosition(StaffPosition::DRIVER)));
        $this->assertFalse($positions->contains(new StaffPosition(StaffPosition::LOADER)));
    }

    #[Test]
    public function it_compares_positions(): void
    {
        $a = new StaffPositions([StaffPosition::DRIVER]);
        $b = new StaffPositions([StaffPosition::DRIVER]);
        $c = new StaffPositions([StaffPosition::LOADER]);
        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
