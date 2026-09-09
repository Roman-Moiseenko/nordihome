<?php

declare(strict_types=1);

namespace App\Modules\Content\Tests\Unit\Application\Actions\Label;

use App\Modules\Content\Application\Actions\Label\IndexLabelUseCase;
use App\Modules\Content\Application\DTOs\Label\LabelIndexData;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class IndexLabelUseCaseTest extends TestCase
{
    use MockPermission;

    private LabelRepositoryInterface $labelRepository;
    private LabelPostRepositoryInterface $labelPostRepository;
    private IndexLabelUseCase $useCase;

    public function getModuleName(): string
    {
        return 'content';
    }

    public function getEntityName(): string
    {
        return 'label';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->labelRepository = Mockery::mock(LabelRepositoryInterface::class);
        $this->labelPostRepository = Mockery::mock(LabelPostRepositoryInterface::class);
        $this->useCase = new IndexLabelUseCase($this->labelRepository, $this->labelPostRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_paginated_label_dtos_with_counts(): void
    {
        $label = new LabelEntity('Новинка', new Slug('Новинка'));
        $label->id = 3;

        $paginator = new LengthAwarePaginator(new Collection([$label]), 1, 20, 1);

        $this->labelRepository->shouldReceive('paginate')->with(20)->once()->andReturn($paginator);
        $this->labelPostRepository->shouldReceive('countPostsByLabelIds')->with([3])->once()->andReturn([3 => 5]);

        $result = $this->useCase->execute($this->mockUserPermission(view: true), 20);

        $this->assertInstanceOf(LabelIndexData::class, $result->getCollection()->first());
        $this->assertSame(3, $result->getCollection()->first()->id);
        $this->assertSame(5, $result->getCollection()->first()->count);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->labelRepository->shouldNotReceive('paginate');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute($this->mockUserPermission(view: false), 20);
    }
}
