<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Application\Actions\PricingRule\CreatePricingRuleUseCase;
use App\Modules\Accounting\Application\Actions\PricingRule\IndexPricingRuleQuery;
use App\Modules\Accounting\Application\Actions\PricingRule\RemovePricingRuleUseCase;
use App\Modules\Accounting\Application\Actions\PricingRule\UpdatePricingRuleUseCase;
use App\Modules\Accounting\Application\Actions\PricingRule\ViewPricingRuleQuery;
use App\Modules\Accounting\Application\Actions\PricingRuleCategory\SyncPricingRuleCategoriesUseCase;
use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleCreateData;
use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleIndexData;
use App\Modules\Accounting\Application\DTOs\PricingRule\PricingRuleUpdateData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PricingRuleController extends Controller
{
    public function __construct(
        private readonly IndexPricingRuleQuery $indexPricingRuleQuery,
        private readonly CreatePricingRuleUseCase $createPricingRuleUseCase,
        private readonly ViewPricingRuleQuery $viewPricingRuleQuery,
        private readonly UpdatePricingRuleUseCase $updatePricingRuleUseCase,
        private readonly RemovePricingRuleUseCase $removePricingRuleUseCase,
        private readonly SyncPricingRuleCategoriesUseCase $syncPricingRuleCategoriesUseCase,
    ) {
    }

    public function index(UserPermission $userPermission): Response
    {
        $rules = $this->indexPricingRuleQuery->execute($userPermission);

        return Inertia::render('Accounting/PricingRule/Index', [
            'rules' => PricingRuleIndexData::collect($rules),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = PricingRuleCreateData::validateAndCreate($request->all());
        $rule = $this->createPricingRuleUseCase->execute($dto, $userPermission);

        return redirect()
            ->route('admin.accounting.pricing-rule.show', $rule->id)
            ->with('success', 'Правило создано');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $rule = $this->viewPricingRuleQuery->execute($id, $userPermission);

        return Inertia::render('Accounting/PricingRule/Show', [
            'rule' => $rule,
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = PricingRuleUpdateData::validateAndCreate($request->all());
        $rule = $this->updatePricingRuleUseCase->execute($id, $dto, $userPermission);

        return redirect()
            ->route('admin.accounting.pricing-rule.show', $rule->id)
            ->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removePricingRuleUseCase->execute($id, $userPermission);

        return redirect()
            ->route('admin.accounting.pricing-rule.index')
            ->with('success', 'Правило удалено');
    }

    public function syncCategories(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $categoryIds = array_map(
            'intval',
            (array) $request->input('category_ids', [])
        );

        try {
            $this->syncPricingRuleCategoriesUseCase->execute($id, $categoryIds, $userPermission);
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Категории сохранены');
    }
}
