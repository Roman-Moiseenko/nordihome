<?php

namespace App\Modules\Shop\Infrastructure\Services;

use App\Modules\Shop\Application\Interfaces\BreadcrumbProviderInterface;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Exceptions\InvalidBreadcrumbException;
use Diglactic\Breadcrumbs\Exceptions\UnnamedRouteException;

class BreadcrumbService implements BreadcrumbProviderInterface
{
    /**
     * @throws InvalidBreadcrumbException
     * @throws UnnamedRouteException
     */
    public function generate(string $routeName, array $params = []): array
    {
        // Генерируем крошки для именованного маршрута
        $breadcrumbs = Breadcrumbs::generate($routeName, $params);

        $items = [];
        foreach ($breadcrumbs as $crumb) {
            $items[] = [
                'name' => $crumb->title,
                'url'  => $crumb->url,
            ];
        }

        // Если нужно, можно исключить текущую страницу (последний элемент без ссылки)
        // или оставить как есть – для Schema лучше оставить последний элемент без URL,
        // но можно и с URL, Schema позволяет ссылку.
        return $items;
    }
}
