<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Wp;

use App\Modules\Catalog\Application\Actions\Wp\GetCategoryByWpIdUseCase;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetCategoryByWpIdUseCaseTest extends TestCase
{
    private CategoryRepositoryInterface $categoryRepository;
    private GetCategoryByWpIdUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new GetCategoryByWpIdUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_category_without_children(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 5;

        $this->categoryRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn($category);
        $this->categoryRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(false);

        $this->assertSame($category, $this->useCase->execute(42));
    }

    #[Test]
    public function it_returns_null_when_not_found(): void
    {
        $this->categoryRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn(null);
        $this->categoryRepository->shouldNotReceive('hasChildren');

        $this->assertNull($this->useCase->execute(42));
    }

    #[Test]
    public function it_returns_null_when_category_has_children(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 5;

        $this->categoryRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn($category);
        $this->categoryRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(true);

        $this->assertNull($this->useCase->execute(42));
    }
}
