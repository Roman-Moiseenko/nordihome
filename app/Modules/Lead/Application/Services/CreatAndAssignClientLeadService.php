<?php

namespace App\Modules\Lead\Application\Services;

use App\Modules\Auth\Application\Actions\Client\CreateClientUseCase;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Lead\Application\Actions\SetClientLeadUseCase;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SetClientOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreatAndAssignClientLeadService
{

    public function __construct(
        private CreateClientUseCase $createClientUseCase,
        private SetClientLeadUseCase $setClientLeadUseCase,
        private SetClientOrderUseCase $setClientOrderUseCase,
        private LeadRepositoryInterface $leadRepository,
        private CreateOrderLoggerUseCase $loggerUseCase,
    )
    {

    }
    public function execute(int $leadId, ClientCreateData $dto, UserPermission $permission): void
    {
        if (!$permission->can('lead.lead.edit')) throw new AccessDeniedException();

        $client = $this->createClientUseCase->execute($dto, $permission);

        $leadEntity = $this->leadRepository->findById($leadId);
        //Если есть заказ присваиваем ему тоже клиента
        if (!is_null($leadEntity->orderId)) {
            $this->setClientOrderUseCase->execute($leadEntity->orderId, $client->id);

            $log = new OrderLoggerCreateData(action: 'Заказу назначен клиент из лида');
            $this->loggerUseCase->execute($leadEntity->orderId, $log);

        }

        $this->setClientLeadUseCase->execute($leadId, $client->id);
    }
}
