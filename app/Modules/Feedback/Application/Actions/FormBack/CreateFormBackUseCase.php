<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Application\Actions\FormBack;

use App\Modules\Feedback\Application\DTOs\FormBack\FormBackCreateData;
use App\Modules\Feedback\Application\Interfaces\FormBackRepositoryInterface;
use App\Modules\Feedback\Domain\Entities\FormBackEntity;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use Illuminate\Events\Dispatcher;

readonly class CreateFormBackUseCase
{
    public function __construct(
        private FormBackRepositoryInterface $formBackRepository,
        private Dispatcher $dispatcher,
    ) {}

    public function execute(FormBackCreateData $dto): FormBackEntity
    {

        // Определяем form_name из данных формы
        $formName = $dto->data['form'] ?? $dto->data['form_name'] ?? 'unknown';

        $formBack = new FormBackEntity(
            url: $dto->url,
            formName: $formName,
            data: $dto->data,
        );

        $formBack = $this->formBackRepository->save($formBack);

        $leadData = new LeadSourceData(
            id: $formBack->id,
            able: 'feedback.form',
            data: $formBack->data,
        );
        // Отправляем событие
        $this->dispatcher->dispatch(new LeadCollected($leadData));

        return $formBack;
    }
}
