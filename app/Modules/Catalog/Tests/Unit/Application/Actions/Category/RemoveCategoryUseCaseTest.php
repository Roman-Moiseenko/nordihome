<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\RemoveCategoryUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private RemoveCategoryUseCase $useCase;

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
        $this->useCase = new RemoveCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_category_without_children(): void
    {
        $this->categoryRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(false);
        $this->categoryRepository->shouldReceive('delete')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_when_category_has_children(): void
    {
        $this->categoryRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(true);
        $this->categoryRepository->shouldNotReceive('delete');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Нельзя удалить категорию с подкатегориями');

        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('hasChildren');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: false));
    }
}
