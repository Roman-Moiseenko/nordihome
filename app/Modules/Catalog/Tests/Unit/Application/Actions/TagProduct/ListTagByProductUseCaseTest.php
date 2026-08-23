<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\Actions\TagProduct\ListTagByProductUseCase;
use App\Modules\Catalog\Application\DTOs\Tag\TagViewData;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListTagByProductUseCaseTest extends TestCase
{
    private TagProductRepositoryInterface $tagProductRepository;
    private TagRepositoryInterface $tagRepository;
    private ListTagByProductUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagProductRepository = Mockery::mock(TagProductRepositoryInterface::class);
        $this->tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $this->useCase = new ListTagByProductUseCase($this->tagProductRepository, $this->tagRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $this->tagProductRepository->shouldReceive('getTagsByProductId')->with(5)->once()->andReturn([]);
        $this->tagRepository->shouldNotReceive('findByIds');

        $this->assertSame([], $this->useCase->execute(5));
    }

    #[Test]
    public function it_returns_tag_dtos(): void
    {
        $tag = new TagEntity('Скидка', new Slug('Скидка'));
        $tag->id = 3;
        $tag->image_url = 'https://example.com/skidka.jpg';

        $this->tagProductRepository->shouldReceive('getTagsByProductId')->with(5)->once()->andReturn([3]);
        $this->tagRepository->shouldReceive('findByIds')->with([3])->once()->andReturn([$tag]);

        $result = $this->useCase->execute(5);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(TagViewData::class, $result[0]);
        $this->assertSame(3, $result[0]->id);
    }
}
