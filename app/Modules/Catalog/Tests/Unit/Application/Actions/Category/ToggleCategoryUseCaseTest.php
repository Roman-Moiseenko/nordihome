<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\ToggleCategoryUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ToggleCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private ToggleCategoryUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'category';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new ToggleCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_toggles_published_and_updates_descendants(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 5;

        $this->categoryRepository->shouldReceive('getById')->with(5)->once()->andReturn($category);
        $this->categoryRepository->shouldReceive('save')->once()->with($category)->andReturn($category);
        $this->categoryRepository->shouldReceive('getDescendantIds')->with(5)->once()->andReturn([6, 7]);
        $this->categoryRepository->shouldReceive('bulkTogglePublished')->with([6, 7], true)->once();

        $this->useCase->execute(5, $this->mockUserPermission(edit: true));

        $this->assertTrue($category->isPublished());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('getById');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(edit: false));
    }
}
