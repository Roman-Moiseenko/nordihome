<?php

declare(strict_types=1);

namespace App\Modules\Parser\Infrastructure\Persistence;

use App\Modules\Parser\Application\Interfaces\ParserLogRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserLogEntity;
use App\Modules\Parser\Domain\Entities\ParserLogItemEntity;
use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Domain\ValueObjects\PriceChangePayload;
use App\Modules\Parser\Infrastructure\Models\ParserLog;
use App\Modules\Parser\Infrastructure\Models\ParserLogItem;
use Illuminate\Pagination\LengthAwarePaginator;

class ParserLogRepository implements ParserLogRepositoryInterface
{
    public function findByDate(string $date): ?ParserLogEntity
    {
        $model = ParserLog::where('date', $date)->first();

        return $model ? $this->hydrateLog($model) : null;
    }

    public function createLog(string $date): ParserLogEntity
    {
        $model = new ParserLog();
        $model->date = $date;

        $model->save();

        return $this->hydrateLog($model->fresh());
    }

    public function saveLog(ParserLogEntity $log): ParserLogEntity
    {
        $model = $log->id
            ? ParserLog::findOrFail($log->id)
            : new ParserLog();

        $model->date = $log->date;
        $model->staff_id = $log->staffId;
        $model->read_at = $log->readAt;

        $model->save();

        return $this->hydrateLog($model->fresh());
    }

    public function addLogItem(ParserLogItemEntity $item): ParserLogItemEntity
    {
        $model = new ParserLogItem();

        $model->log_id = $item->logId;
        $model->parser_id = $item->parserId;
        $model->status = $item->status->value;
        $model->error = $item->error;

        if ($item->payload !== null) {
            $model->payload = $item->payload->toArray();
        }

        $model->save();

        return $this->hydrateLogItem($model->fresh());
    }

    public function getLogsPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return ParserLog::query()
            ->orderByDesc('date')
            ->paginate($perPage)
            ->through(fn(ParserLog $model) => $this->hydrateLog($model));
    }

    public function getLogItemsPaginated(int $logId, int $perPage = 20): LengthAwarePaginator
    {
        return ParserLogItem::query()
            ->where('log_id', $logId)
            ->paginate($perPage)
            ->through(fn(ParserLogItem $model) => $this->hydrateLogItem($model));
    }

    public function markAsRead(int $logId, int $staffId): ParserLogEntity
    {
        $model = ParserLog::findOrFail($logId);

        $model->staff_id = $staffId;
        $model->read_at = now();

        $model->save();

        return $this->hydrateLog($model->fresh());
    }

    private function hydrateLog(ParserLog $model): ParserLogEntity
    {
        $entity = new ParserLogEntity(
            date: $model->date,
        );

        $entity->id = $model->id;
        $entity->staffId = $model->staff_id;
        $entity->readAt = $model->read_at instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromMutable($model->read_at)
            : $model->read_at;

        return $entity;
    }

    private function hydrateLogItem(ParserLogItem $model): ParserLogItemEntity
    {
        $entity = new ParserLogItemEntity(
            logId: $model->log_id,
            status: ParserStatus::from($model->status),
            parserId: $model->parser_id,
            payload: $model->payload ? PriceChangePayload::fromArray($model->payload) : null,
            error: $model->error,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
