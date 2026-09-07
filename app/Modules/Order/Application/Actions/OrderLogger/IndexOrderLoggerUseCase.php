<?php

namespace App\Modules\Order\Application\Actions\OrderLogger;

use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerIndexData;
use App\Modules\Order\Domain\Entities\OrderLoggerEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Infrastructure\Models\OrderLogger;


readonly class IndexOrderLoggerUseCase
{
    public function __construct(
        private OrderLoggerRepositoryInterface $loggerRepository,
        private StaffRepositoryInterface $staffRepository,
    )
    {

    }

    public function execute(int $orderId)
    {
        $logs = $this->loggerRepository->getByOrderId($orderId);

        return array_map(function (OrderLoggerEntity $log) {
            $staff = $this->staffRepository->findById($log->staffId);
            return OrderLoggerIndexData::fromEntity($log, $staff->fullName->getValue());
        }, $logs);

        //return OrderLoggerIndexData::collect($logs);

    }
}
