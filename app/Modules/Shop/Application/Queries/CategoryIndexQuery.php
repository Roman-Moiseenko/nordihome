<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\CategoryIndexPageData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategoryTreeQueryRepository;
use Illuminate\Support\Facades\Cache;

class CategoryIndexQuery
{
    public function __construct(
        private readonly CategoryTreeQueryRepository $treeRepo,
        private readonly Settings $settings,
    )
    {
    }

    public function execute(): CategoryIndexPageData
    {
        $web = $this->settings->web;

        $categories = Cache::remember(
            'category_root_list',
            now()->addDay(),
            fn() => $this->treeRepo->getChildren(),
        );

        return new CategoryIndexPageData(
            meta: new SeoData(
                title: $web->categories_title,
                description: $web->categories_desc,
            ),
            categories: $categories,
        );
    }
}
