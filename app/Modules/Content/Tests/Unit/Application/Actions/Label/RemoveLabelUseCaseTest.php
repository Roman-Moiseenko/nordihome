<?php

declare(strict_types=1);

namespace App\Modules\Content\Tests\Unit\Application\Actions\Label;

use App\Modules\Content\Application\Actions\Label\RemoveLabelUseCase;
use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveLabelUseCaseTest extends TestCase
{
    use MockPermission;

    private LabelRepositoryInterface $labelRepository;
    private LabelPostRepositoryInterface $labelPostRepository;
    private RemoveLabelUseCase $useCase;

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
        $this->useCase = new RemoveLabelUseCase($this->labelRepository, $this->labelPostRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_label_without_posts(): void
    {
        $this->labelPostRepository->shouldReceive('countPostsByLabelId')->with(5)->once()->andReturn(0);
        $this->labelRepository->shouldReceive('delete')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_when_label_has_posts(): void
    {
        $this->labelPostRepository->shouldReceive('countPostsByLabelId')->with(5)->once()->andReturn(2);
        $this->labelRepository->shouldNotReceive('delete');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->labelPostRepository->shouldNotReceive('countPostsByLabelId');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: false));
    }
}
