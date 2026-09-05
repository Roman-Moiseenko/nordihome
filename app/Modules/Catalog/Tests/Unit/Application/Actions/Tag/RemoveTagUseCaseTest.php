<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Tag;

use App\Modules\Catalog\Application\Actions\Tag\RemoveTagUseCase;
use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagRepositoryInterface $tagRepository;
    private TagProductRepositoryInterface $tagProductRepository;
    private RemoveTagUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'product';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $this->tagProductRepository = Mockery::mock(TagProductRepositoryInterface::class);
        $this->useCase = new RemoveTagUseCase($this->tagRepository, $this->tagProductRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_tag_without_products(): void
    {
        $this->tagProductRepository->shouldReceive('countProductsByTagId')->with(5)->once()->andReturn(0);
        $this->tagRepository->shouldReceive('delete')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_when_tag_has_products(): void
    {
        $this->tagProductRepository->shouldReceive('countProductsByTagId')->with(5)->once()->andReturn(2);
        $this->tagRepository->shouldNotReceive('delete');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->tagProductRepository->shouldNotReceive('countProductsByTagId');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: false));
    }
}
