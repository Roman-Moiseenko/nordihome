<?php

namespace App\Modules\Shop\Infrastructure\Persistence;

use App\Modules\Base\Entity\Meta;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;

readonly class SeoAdapter
{
    public function __construct(
        private MetaTemplateRepository $seoService
    ) {}

    /**
     * Получить SEO-данные для категории, используя CategoryInfo (DTO).
     */
    public function getSeoFromCategoryInfo(CategoryInfo $categoryInfo): Meta
    {
        // Создаём временную модель, которая никогда не будет сохранена в БД
        $fakeModel = new Category();

        // Наполняем атрибутами, которые используются в шаблонах SEO (get_class + поля name, description, title)
        $fakeModel->forceFill([
            'id'          => $categoryInfo->id,
            'name'        => $categoryInfo->name,
            'description' => $categoryInfo->description ?? '',
            'title'       => $categoryInfo->title ?? '',
            // другие поля, если они понадобятся в будущем
        ]);

        // Помечаем модель как существующую, чтобы get_class($fakeModel) вернул Category::class
        $fakeModel->exists = true;

        // Вызываем старый сервис – он определит класс модели, найдёт шаблон и отрендерит мета-теги
        return $this->seoService->seo($fakeModel);

    }
}
