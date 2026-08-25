<?php

namespace App\Modules\Lead\Tests\Unit\Domain\ValueObjects;

use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use PHPUnit\Framework\TestCase;

class LeadDataFieldTest extends TestCase
{
    public function test_creates_from_constructor(): void
    {
        $field = new LeadDataField(name: 'phone', value: '+79991234567');

        $this->assertSame('phone', $field->getName());
        $this->assertSame('+79991234567', $field->getValue());
    }

    public function test_from_array_with_values(): void
    {
        $field = LeadDataField::fromArray(['name' => 'email', 'value' => 'test@example.com']);

        $this->assertSame('email', $field->getName());
        $this->assertSame('test@example.com', $field->getValue());
    }

    public function test_from_array_with_missing_keys_returns_empty_strings(): void
    {
        $field = LeadDataField::fromArray([]);

        $this->assertSame('', $field->getName());
        $this->assertSame('', $field->getValue());
    }
}
