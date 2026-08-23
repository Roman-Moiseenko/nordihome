<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\UpdateCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Category\CategoryUpdateData;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private UpdateCategoryUseCase $useCase;

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
        $this->useCase = new UpdateCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeCategory(): CategoryEntity
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 5;
        return $category;
    }

    #[Test]
    public function it_updates_fields_and_saves(): void
    {
        $category = $this->makeCategory();

        $this->categoryRepository->shouldReceive('getById')->with(5)->once()->andReturn($category);
        $this->categoryRepository->shouldReceive('existsSlug')->with('new-slug', 5)->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('save')->once()->with($category)->andReturn($category);

        $dto = new CategoryUpdateData(
            name: 'Новое имя',
            slug: 'new-slug',
            parentId: null,
            svgIcon: null,
            published: null,
            metaTitle: null,
            metaDescription: null,
        );

        $result = $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: true));

        $this->assertSame('Новое имя', $result->name);
        $this->assertSame('new-slug', $result->slug->getValue());
    }

    #[Test]
    public function it_publishes_when_requested(): void
    {
        $category = $this->makeCategory();

        $this->categoryRepository->shouldReceive('getById')->with(5)->once()->andReturn($category);
        $this->categoryRepository->shouldNotReceive('existsSlug');
        $this->categoryRepository->shouldReceive('save')->once()->with($category)->andReturn($category);

        $dto = new CategoryUpdateData(
            name: null,
            slug: null,
            parentId: null,
            svgIcon: null,
            published: true,
            metaTitle: null,
            metaDescription: null,
        );

        $result = $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: true));

        $this->assertTrue($result->isPublished());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('getById');

        $dto = new CategoryUpdateData(
            name: null,
            slug: null,
            parentId: null,
            svgIcon: null,
            published: null,
            metaTitle: null,
            metaDescription: null,
        );

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: false));
    }
}
