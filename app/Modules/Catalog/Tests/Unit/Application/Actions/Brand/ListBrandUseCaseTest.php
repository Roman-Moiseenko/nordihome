<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Brand;

use App\Modules\Catalog\Application\Actions\Brand\ListBrandUseCase;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListBrandUseCaseTest extends TestCase
{
    private BrandRepositoryInterface $brandRepository;
    private ListBrandUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->brandRepository = Mockery::mock(BrandRepositoryInterface::class);
        $this->useCase = new ListBrandUseCase($this->brandRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_brands_as_arrays(): void
    {
        $brand = new BrandEntity('Икеа');
        $brand->id = 1;
        $brand->parserClass = 'IkeaParser';

        $this->brandRepository->shouldReceive('getAll')->once()->andReturn([$brand]);

        $result = $this->useCase->execute();

        $this->assertSame([
            ['id' => 1, 'name' => 'Икеа', 'parser' => 'IkeaParser'],
        ], $result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_brands(): void
    {
        $this->brandRepository->shouldReceive('getAll')->once()->andReturn([]);

        $this->assertSame([], $this->useCase->execute());
    }
}
