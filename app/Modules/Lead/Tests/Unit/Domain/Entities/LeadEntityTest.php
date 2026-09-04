<?php

namespace App\Modules\Lead\Tests\Unit\Domain\Entities;

use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use App\Modules\Lead\Domain\Entities\LeadStatusEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use PHPUnit\Framework\TestCase;

class LeadEntityTest extends TestCase
{
    private function makeLead(): LeadEntity
    {
        return new LeadEntity(leadableId: 10, leadableType: 'order', data: []);
    }

    public function test_constructs_with_leadable(): void
    {
        $lead = $this->makeLead();

        $this->assertSame(10, $lead->leadableId);
        $this->assertSame('order', $lead->leadableType);
        $this->assertSame([], $lead->data);
        $this->assertSame([], $lead->statuses);
        $this->assertSame([], $lead->items);
    }

    public function test_add_status_sets_current_status(): void
    {
        $lead = $this->makeLead();

        $lead->addStatus(new LeadStatusValue(LeadStatusValue::NEW_LEAD));

        $this->assertCount(1, $lead->statuses);
        $this->assertInstanceOf(LeadStatusEntity::class, $lead->status);
        $this->assertSame('new', $lead->status->value->getValue());
    }

    public function test_add_status_entity_updates_current_status(): void
    {
        $lead = $this->makeLead();

        $lead->addStatusEntity(new LeadStatusEntity(new LeadStatusValue(LeadStatusValue::NEW_LEAD)));
        $lead->addStatusEntity(new LeadStatusEntity(new LeadStatusValue(LeadStatusValue::IN_WORK)));

        $this->assertCount(2, $lead->statuses);
        $this->assertSame(LeadStatusValue::IN_WORK, $lead->status->value->getValue());
    }

    public function test_add_item(): void
    {
        $lead = $this->makeLead();

        $lead->addItem(new LeadItemEntity(comment: 'Комментарий', staffId: 7));

        $this->assertCount(1, $lead->items);
        $this->assertSame('Комментарий', $lead->items[0]->comment);
        $this->assertSame(7, $lead->items[0]->staffId);
    }

    public function test_add_data_field(): void
    {
        $lead = $this->makeLead();

        $lead->addDataField(new LeadDataField(name: 'phone', value: '+79991234567'));

        $this->assertCount(1, $lead->data);
        $this->assertSame('phone', $lead->data[0]->getName());
        $this->assertSame('+79991234567', $lead->data[0]->getValue());
    }
}
