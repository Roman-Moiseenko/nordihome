<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;

final readonly class RemoveWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $widgetInstanceRepository,
    ) {}

    public function execute(int $id): void
    {
        // Найти все экземпляры, которые ссылаются на удаляемый
        $allInstances = $this->widgetInstanceRepository->getAll();

        foreach ($allInstances as $child) {
            $params = $child->params;
            $modifiedParams = $this->clearWidgetInstanceReferences($params, $id);
            if ($modifiedParams !== $params) {
                $child->params = $modifiedParams;
                $this->widgetInstanceRepository->save($child);
            }
        }

        // Удалить сам экземпляр
        $this->widgetInstanceRepository->delete($id);
    }

    /**
     * Рекурсивно очищает ссылки на удаляемый экземпляр в params.
     *
     * @param array $params
     * @param int $removedInstanceId
     * @return array
     */
    private function clearWidgetInstanceReferences(array $params, int $removedInstanceId): array
    {
        foreach ($params as $key => $value) {
            if ($value === $removedInstanceId) {
                $params[$key] = null;
            } elseif (is_array($value)) {
                $params[$key] = $this->clearWidgetInstanceReferences($value, $removedInstanceId);
            }
        }
        return $params;
    }
}
