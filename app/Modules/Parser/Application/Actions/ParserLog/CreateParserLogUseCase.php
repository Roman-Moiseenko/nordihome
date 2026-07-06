<?php

namespace App\Modules\Parser\Application\Actions\ParserLog;

use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogCreateData;
use App\Modules\Parser\Application\Interfaces\ParserLogRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserLogItemEntity;

class CreateParserLogUseCase
{

    public function __construct(public readonly ParserLogRepositoryInterface $parserLogRepository)
    {
    }

    public function execute(ParserLogCreateData $dto): void
    {
        $date = now()->toDateString();

        if (is_null($log = $this->parserLogRepository->findByDate($date))) {
            $log = $this->parserLogRepository->createLog($date);
        }
        $item = new ParserLogItemEntity(
            logId: $log->id,
            status: $dto->status,
            parserId: $dto->parserId,
            payload: $dto->payload,
            error: $dto->error,
        );
        $this->parserLogRepository->addLogItem($item);
    }
}
