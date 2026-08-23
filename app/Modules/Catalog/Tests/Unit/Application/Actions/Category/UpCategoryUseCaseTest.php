<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Category;

use App\Modules\Catalog\Application\Actions\Category\UpCategoryUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryRepositoryInterface $categoryRepository;
    private UpCategoryUseCase $useCase;

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
        $this->useCase = new UpCategoryUseCase($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_moves_category_up(): void
    {
        $this->categoryRepository->shouldReceive('moveUp')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->categoryRepository->shouldNotReceive('moveUp');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(edit: false));
    }
}
