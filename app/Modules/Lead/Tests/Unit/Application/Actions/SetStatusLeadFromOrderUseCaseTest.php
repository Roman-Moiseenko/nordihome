<?php

namespace App\Modules\Lead\Tests\Unit\Application\Actions;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class SetStatusLeadFromOrderUseCaseTest extends TestCase
{
    private LeadRepositoryInterface $leadRepository;
    private SetStatusLeadFromOrderUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leadRepository = Mockery::mock(LeadRepositoryInterface::class);
        $this->useCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeLead(): LeadEntity
    {
        return new LeadEntity(leadableId: 10, leadableType: 'order', data: []);
    }

    public function test_adds_status_to_lead(): void
    {
        $lead = $this->makeLead();

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);

        $this->useCase->execute(10, LeadStatusValue::IN_WORK);

        $this->assertCount(1, $lead->statuses);
        $this->assertSame('draft', $lead->status->value->getValue());
    }

    public function test_throws_on_invalid_status(): void
    {
        $lead = $this->makeLead();

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);

        $this->expectException(InvalidArgumentException::class);
        $this->useCase->execute(10, 'unknown-status');
    }
}
