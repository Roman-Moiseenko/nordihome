<?php

namespace App\Modules\Auth\Tests\Unit\Domain\Entities;

use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StaffTest extends TestCase
{
    private FullName $fullName;
    private PhoneNumber $workPhone;
    private Email $workEmail;

    protected function setUp(): void
    {
        $this->fullName = new FullName('Иванов Иван Иванович');
        $this->workPhone = new PhoneNumber('+79001234567');
        $this->workEmail = new Email('ivanov@example.com');
    }

    #[Test]
    public function it_can_be_created_with_minimum_required_fields(): void
    {
        $staff = new StaffEntity(
            $this->fullName,
            new StaffPositions([StaffPosition::CUSTOMER_MANAGER]),
        );

        $this->assertNull($staff->id);
        $this->assertEquals($this->fullName, $staff->fullName);
        $this->assertEquals([StaffPosition::CUSTOMER_MANAGER], $staff->positions->toArrayOfStrings());
        $this->assertNull($staff->department);
        $this->assertNull($staff->workPhone);
        $this->assertNull($staff->workEmail);
        $this->assertTrue($staff->isActive);
        $this->assertNull($staff->hireDate);
        $this->assertNull($staff->terminationDate);
        $this->assertNull($staff->personalPhone);
        $this->assertNull($staff->birthDate);
        $this->assertNull($staff->notes);
    }

    #[Test]
    public function it_can_set_and_get_id(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $staff->id = 42;
        $this->assertEquals(42, $staff->id);
    }

    #[Test]
    public function it_can_set_optional_fields(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $staff->department = 'IT';
        $staff->workPhone = $this->workPhone;
        $staff->workEmail = $this->workEmail;
        $staff->personalPhone = new PhoneNumber('+79005556677');

        $this->assertEquals('IT', $staff->department);
        $this->assertEquals($this->workPhone, $staff->workPhone);
        $this->assertEquals($this->workEmail, $staff->workEmail);
        $this->assertEquals(new PhoneNumber('+79005556677'), $staff->personalPhone);
    }

    #[Test]
    public function it_can_set_hire_date(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $hireDate = new DateTimeImmutable('2025-01-15');
        $staff->hireDate = $hireDate;
        $this->assertEquals($hireDate, $staff->hireDate);
    }

    #[Test]
    public function it_can_set_birth_date(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $birthDate = new DateTimeImmutable('1990-05-20');
        $staff->birthDate = $birthDate;
        $this->assertEquals($birthDate, $staff->birthDate);
    }

    #[Test]
    public function it_can_set_telegram_chat_id(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $staff->telegramChatId = '123456789';
        $this->assertEquals('123456789', $staff->telegramChatId);
    }

    #[Test]
    public function it_can_set_notes(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $staff->notes = 'Важный сотрудник';
        $this->assertEquals('Важный сотрудник', $staff->notes);
    }

    #[Test]
    public function it_can_terminate_and_rehire(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $this->assertTrue($staff->isActive);
        $this->assertNull($staff->terminationDate);

        $terminationDate = new DateTimeImmutable('2025-06-01');
        $staff->terminate($terminationDate);

        $this->assertFalse($staff->isActive);
        $this->assertEquals($terminationDate, $staff->terminationDate);

        $staff->rehire();
        $this->assertTrue($staff->isActive);
        $this->assertNull($staff->terminationDate);
    }

    #[Test]
    public function it_can_update_full_name(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $newFullName = new FullName('Петров Пётр Петрович');
        $staff->fullName = $newFullName;
        $this->assertEquals($newFullName, $staff->fullName);
    }

    #[Test]
    public function it_can_update_positions_and_department(): void
    {
        $staff = new StaffEntity($this->fullName, new StaffPositions([StaffPosition::DRIVER]));
        $staff->positions = new StaffPositions([StaffPosition::SUPERVISOR, StaffPosition::CUSTOMER_MANAGER]);
        $staff->department = 'Продажи';

        $this->assertEquals(
            [StaffPosition::SUPERVISOR, StaffPosition::CUSTOMER_MANAGER],
            $staff->positions->toArrayOfStrings()
        );
        $this->assertEquals('Продажи', $staff->department);
    }
}
