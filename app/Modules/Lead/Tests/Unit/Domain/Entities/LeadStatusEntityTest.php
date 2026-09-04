<?php

namespace App\Modules\Lead\Tests\Unit\Domain\Entities;

use App\Modules\Lead\Domain\Entities\LeadStatusEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class LeadStatusEntityTest extends TestCase
{
    public function test_constructs_status(): void
    {
        $status = new LeadStatusEntity(new LeadStatusValue(LeadStatusValue::IN_WORK));

        $this->assertNull($status->id);
        $this->assertSame(LeadStatusValue::IN_WORK, $status->value->getValue());
        $this->assertInstanceOf(DateTimeImmutable::class, $status->createdAt);
    }
}
