<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Application\Queries\Category\GetCategoryTreeQuery;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\View\View;

class CategoryComposer
{
    public function __construct(
        private GetCategoryTreeQuery $query
    ) {}

    /**
     * @throws LockTimeoutException
     */
    public function compose(View $view): void
    {
        // Передаём дерево во все представления, которым нужен composer
        $view->with('categoryTree', $this->query->execute());
    }
}
