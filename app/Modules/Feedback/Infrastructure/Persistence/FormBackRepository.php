<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Infrastructure\Persistence;

use App\Modules\Feedback\Application\Interfaces\FormBackRepositoryInterface;
use App\Modules\Feedback\Domain\Entities\FormBackEntity;
use App\Modules\Feedback\Infrastructure\Models\FormBack;
use Illuminate\Pagination\LengthAwarePaginator;

class FormBackRepository implements FormBackRepositoryInterface
{
    public function save(FormBackEntity $formBack): FormBackEntity
    {
        $model = $formBack->id
            ? FormBack::findOrFail($formBack->id)
            : new FormBack();

        $model->url = $formBack->url;
        $model->form_name = $formBack->formName;
        $model->data = $formBack->data;
        $model->created_at = $formBack->createdAt?->format('Y-m-d H:i:s');

        $model->save();

        $model->refresh();

        return $this->hydrate($model);
    }

    public function getById(int $id): FormBackEntity
    {
        $model = FormBack::findOrFail($id);

        return $this->hydrate($model);
    }

    public function getAll(): LengthAwarePaginator
    {
        return FormBack::orderByDesc('created_at')
            ->paginate(20);
    }

    private function hydrate(FormBack $model): FormBackEntity
    {
        $entity = new FormBackEntity(
            url: $model->url,
            formName: $model->form_name,
            data: $model->data ?? [],
        );

        $entity->id = $model->id;
        $entity->createdAt = $model->created_at instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($model->created_at)
            : null;

        return $entity;
    }
}
