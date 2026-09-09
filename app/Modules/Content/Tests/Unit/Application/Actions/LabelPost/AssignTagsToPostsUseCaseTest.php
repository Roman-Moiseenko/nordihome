<?php

declare(strict_types=1);

namespace App\Modules\Content\Tests\Unit\Application\Actions\LabelPost;

use App\Modules\Content\Application\Actions\LabelPost\AssignTagsToPostsUseCase;
use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelPostRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AssignTagsToPostsUseCaseTest extends TestCase
{
    use MockPermission;

    private LabelPostRepositoryInterface $labelPostRepository;
    private LabelRepositoryInterface $labelRepository;
    private AssignTagsToPostsUseCase $useCase;

    public function getModuleName(): string
    {
        return 'content';
    }

    public function getEntityName(): string
    {
        return 'post';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->labelPostRepository = Mockery::mock(LabelPostRepositoryInterface::class);
        $this->labelRepository = Mockery::mock(LabelRepositoryInterface::class);
        $this->useCase = new AssignTagsToPostsUseCase($this->labelPostRepository, $this->labelRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_syncs_existing_label_ids(): void
    {
        $this->labelPostRepository->shouldReceive('syncLabels')->with(5, [1, 2])->once();

        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_creates_new_labels_from_strings(): void
    {
        $newLabel = new LabelEntity('Новинка', new Slug('Новинка'));
        $newLabel->id = 10;

        $this->labelRepository->shouldReceive('findByName')->with('Новинка')->once()->andReturn(null);
        $this->labelRepository->shouldReceive('existsSlug')->with('novinka')->once()->andReturn(false);
        $this->labelRepository->shouldReceive('save')->once()->andReturn($newLabel);
        $this->labelPostRepository->shouldReceive('syncLabels')->with(5, [10])->once();

        $this->useCase->execute(5, ['Новинка'], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_reuses_existing_label_by_name(): void
    {
        $existing = new LabelEntity('Новинка', new Slug('novinka'));
        $existing->id = 7;

        $this->labelRepository->shouldReceive('findByName')->with('Новинка')->once()->andReturn($existing);
        $this->labelPostRepository->shouldReceive('syncLabels')->with(5, [7])->once();

        $this->useCase->execute(5, ['Новинка'], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_handles_mixed_ids_and_names(): void
    {
        $newLabel = new LabelEntity('Акция', new Slug('akciya'));
        $newLabel->id = 11;

        $this->labelRepository->shouldReceive('findByName')->with('Акция')->once()->andReturn(null);
        $this->labelRepository->shouldReceive('existsSlug')->with('akciya')->once()->andReturn(false);
        $this->labelRepository->shouldReceive('save')->once()->andReturn($newLabel);
        $this->labelPostRepository->shouldReceive('syncLabels')->with(5, [1, 11])->once();

        $this->useCase->execute(5, [1, 'Акция'], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_clears_labels_on_empty_array(): void
    {
        $this->labelPostRepository->shouldReceive('syncLabels')->with(5, [])->once();

        $this->useCase->execute(5, [], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->labelPostRepository->shouldNotReceive('syncLabels');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, [1], $this->mockUserPermission(edit: false));
    }
}
