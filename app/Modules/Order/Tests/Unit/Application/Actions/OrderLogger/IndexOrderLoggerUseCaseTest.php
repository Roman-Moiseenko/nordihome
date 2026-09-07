<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderLogger;

use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Order\Application\Actions\OrderLogger\IndexOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerIndexData;
use App\Modules\Order\Domain\Entities\OrderLoggerEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class IndexOrderLoggerUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_logs_with_staff_names(): void
    {
        $loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $staffRepository = Mockery::mock(StaffRepositoryInterface::class);

        $log = new OrderLoggerEntity(orderId: 10, staffId: 3, action: 'Заказ создан');
        $log->id = 1;
        $log->createdAt = new DateTimeImmutable('2026-09-07 12:00:00');
        $log->object = 'объект';
        $log->old = 'старое';
        $log->value = 'новое';
        $log->link = 'http://link';

        $staff = new StaffEntity(new FullName('Иванов Иван'), new StaffPositions(['customer_manager']));

        $loggerRepository->shouldReceive('getByOrderId')->with(10)->once()->andReturn([$log]);
        $staffRepository->shouldReceive('findById')->with(3)->once()->andReturn($staff);

        $useCase = new IndexOrderLoggerUseCase($loggerRepository, $staffRepository);
        $result = $useCase->execute(10);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(OrderLoggerIndexData::class, $result[0]);
        $this->assertSame('Заказ создан', $result[0]->action);
        $this->assertSame('Иванов Иван', $result[0]->staffName);
    }

    public function test_returns_empty_array_when_no_logs(): void
    {
        $loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $staffRepository = Mockery::mock(StaffRepositoryInterface::class);

        $loggerRepository->shouldReceive('getByOrderId')->with(10)->once()->andReturn([]);

        $useCase = new IndexOrderLoggerUseCase($loggerRepository, $staffRepository);
        $result = $useCase->execute(10);

        $this->assertSame([], $result);
    }
}
