<?php

namespace App\Modules\Catalog\Tests\Unit\Domain\ValueObjects;

use App\Modules\Catalog\Domain\ValueObjects\Code;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    #[Test]
    public function it_creates_code_and_normalizes_code_search(): void
    {
        $code = new Code('ABC-123,45.6:7_8');

        $this->assertSame('ABC-123,45.6:7_8', $code->getCode());
        $this->assertSame('ABC12345678', $code->getCodeSearch());
    }

    #[Test]
    public function it_creates_from_database_values(): void
    {
        $code = Code::fromDatabase('ABC-123', 'ABC123');

        $this->assertSame('ABC-123', $code->getCode());
        $this->assertSame('ABC123', $code->getCodeSearch());
    }

    #[Test]
    public function it_throws_on_empty_code(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Code('');
    }

    #[Test]
    public function it_casts_to_string(): void
    {
        $code = new Code('ABC-123');

        $this->assertSame('ABC-123', (string) $code);
    }
}
