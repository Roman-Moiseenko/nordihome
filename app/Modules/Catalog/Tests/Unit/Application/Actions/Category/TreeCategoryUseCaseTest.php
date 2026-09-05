<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\TreeCategoryUseCase;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TreeCategoryUseCaseTest extends TestCase
{
    private CategoryRepositoryInterface $categoryRepository;
    private TreeCategoryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new TreeCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_tree(): void
    {
        $tree = [new CategoryEntity('Мебель', new Slug('Мебель'))];

        $this->categoryRepository->shouldReceive('getTree')->once()->andReturn($tree);

        $this->assertSame($tree, $this->useCase->execute());
    }
}
