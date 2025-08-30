<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\BankDetail;
use App\Modules\Base\Entity\CompanyContact;
use App\Modules\Base\Entity\CompanyDetail;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Product\Entity\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraderService
{
    private OrganizationService $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request): Trader
    {
        DB::transaction(function () use ($request, &$trader) {
            $trader = Trader::register(
                $request->string('name')->trim()->value()
                );

            $organization = $this->service->create_find(
                $request->string('inn')->trim()->value(),
                $request->string('bik')->trim()->value(),
                $request->string('account')->trim()->value()
            );
            $this->attach($trader, $organization->id);
        });
        return $trader;
    }

    public function destroy(Trader $trader)
    {
        throw new \DomainException('Удалять программно нельзя!');
       // $trader->delete();
    }

    public function attach(Trader $trader, int $organization_id): void
    {
        foreach ($trader->organizations as $organization) {
            if ($organization->id == $organization_id) throw new \DomainException('Организация уже назначена!');
        }
        $default = is_null($trader->organization);
        $trader->organizations()->attach($organization_id, ['default' => $default]);
    }

    public function detach(Trader $trader, int $organization_id): void
    {
        $trader->organizations()->detach($organization_id);
    }

    public function default(Trader $trader, int $organization_id): void
    {
        foreach ($trader->organizations as $organization) {
            $trader->organizations()->updateExistingPivot($organization->id, ['default' => false]);
        }
        $trader->organizations()->updateExistingPivot($organization_id, ['default' => true]);
    }

    public function setInfo(Trader $trader, Request $request): void
    {
        $trader->name = $request->string('name')->value();

        if (!$trader->default && $request->boolean('default')) {
            Trader::where('default', true)->update(['default' => true]);
            $trader->default = true;
        }
        $trader->save();
        if ($trader->default && !$request->boolean('default')) throw new \DomainException('Нельзя отменить по-умолчанию');
    }
}
