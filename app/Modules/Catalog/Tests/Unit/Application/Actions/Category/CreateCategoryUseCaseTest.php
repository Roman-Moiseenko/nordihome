<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\CreateCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Category\CategoryCreateData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private CreateCategoryUseCase $useCase;

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
        $this->useCase = new CreateCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_and_saves_category(): void
    {
        $this->categoryRepository->shouldReceive('existsSlug')->with('mebel')->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(CategoryEntity $category) => $category->name === 'Мебель' && $category->parentId === null))
            ->andReturnUsing(fn(CategoryEntity $category) => $category);

        $dto = new CategoryCreateData(name: 'Мебель', slug: null, parentId: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertInstanceOf(CategoryEntity::class, $result);
        $this->assertSame('Мебель', $result->name);
    }

    #[Test]
    public function it_uses_given_slug(): void
    {
        $this->categoryRepository->shouldReceive('existsSlug')->with('custom-slug')->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('save')->once()->andReturnUsing(fn(CategoryEntity $category) => $category);

        $dto = new CategoryCreateData(name: 'Мебель', slug: 'custom-slug', parentId: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertSame('custom-slug', $result->slug->getValue());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('existsSlug');
        $this->categoryRepository->shouldNotReceive('save');

        $dto = new CategoryCreateData(name: 'Мебель', slug: null, parentId: null);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Доступ запрещён');

        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
