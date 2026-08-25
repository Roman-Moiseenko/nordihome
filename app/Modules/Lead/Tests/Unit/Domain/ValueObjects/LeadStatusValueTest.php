<?php

namespace App\Modules\Lead\Tests\Unit\Domain\ValueObjects;

use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LeadStatusValueTest extends TestCase
{
    public function test_creates_valid_status(): void
    {
        $status = new LeadStatusValue(LeadStatusValue::NEW_LEAD);

        $this->assertSame('new', $status->getValue());
        $this->assertSame('Новый', $status->getName());
        $this->assertSame('new', (string) $status);
    }

    public function test_names_of_all_statuses(): void
    {
        $this->assertSame('В работе', (new LeadStatusValue(LeadStatusValue::IN_WORK))->getName());
        $this->assertSame('Клиент думает', (new LeadStatusValue(LeadStatusValue::NOT_DECIDED))->getName());
        $this->assertSame('Выставлен счет', (new LeadStatusValue(LeadStatusValue::INVOICE))->getName());
        $this->assertSame('Оплачен', (new LeadStatusValue(LeadStatusValue::PAID))->getName());
        $this->assertSame('На сборке', (new LeadStatusValue(LeadStatusValue::ASSEMBLY))->getName());
        $this->assertSame('На доставке', (new LeadStatusValue(LeadStatusValue::DELIVERY))->getName());
        $this->assertSame('Отменен', (new LeadStatusValue(LeadStatusValue::CANCELLED))->getName());
        $this->assertSame('Завершен', (new LeadStatusValue(LeadStatusValue::COMPLETED))->getName());
    }

    public function test_throws_on_invalid_status(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LeadStatusValue('unknown-status');
    }
}
