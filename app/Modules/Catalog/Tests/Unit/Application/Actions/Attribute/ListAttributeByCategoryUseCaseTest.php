<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Attribute;

use App\Modules\Catalog\Application\Actions\Attribute\ListAttributeByCategoryUseCase;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListAttributeByCategoryUseCaseTest extends TestCase
{
    private AttributeRepositoryInterface $attributeRepository;
    private ListAttributeByCategoryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attributeRepository = Mockery::mock(AttributeRepositoryInterface::class);
        $this->useCase = new ListAttributeByCategoryUseCase($this->attributeRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_attributes_grouped_by_self_and_parent(): void
    {
        $result = ['self' => [], 'parent' => []];

        $this->attributeRepository->shouldReceive('findForCategory')->with(5)->once()->andReturn($result);

        $this->assertSame($result, $this->useCase->execute(5));
    }
}
