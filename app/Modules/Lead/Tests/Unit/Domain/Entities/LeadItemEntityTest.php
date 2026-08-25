<?php

namespace App\Modules\Lead\Tests\Unit\Domain\Entities;

use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use PHPUnit\Framework\TestCase;

class LeadItemEntityTest extends TestCase
{
    public function test_constructs_item(): void
    {
        $item = new LeadItemEntity(comment: 'Комментарий', staffId: 7);

        $this->assertSame('Комментарий', $item->comment);
        $this->assertSame(7, $item->staffId);
        $this->assertNull($item->id);
        $this->assertNull($item->type);
        $this->assertNull($item->finishedAt);
    }
}
