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

class TraderService
{

    private OrganizationService $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request): Trader
    {
        $trader = Trader::register(
            $request->string('name')->trim()->value()
            );

        if (($organization_id = $request->integer('organization_id')) > 0) {
            $trader->organization_id = $organization_id;
        } else {
            if (empty($request->input('inn'))) throw new \DomainException('Не выбрана организация, необходимо привязать компанию к поставщику');
            $organization = $this->service->create($request);
            $trader->organization_id = $organization->id;
        }
        $trader->save();
        $this->save_fields($request, $trader);
        return $trader;
    }

    public function update(Request $request, Trader $trader): Trader
    {
        $trader->name = $request->string('name')->trim()->value();
        $trader->organization_id = $request->integer('organization_id');
        $trader->save();

        $this->save_fields($request, $trader);
        return $trader;
    }

    public function save_fields(Request $request, Trader $trader)
    {
        if (isset($request['default'])) {
            foreach (Trader::get() as $item) {
                $item->default = false;
                $item->save();
            }
            $trader->default = true;
        }
        $trader->active = $request->boolean('active');
        $trader->save();
    }

    public function destroy(Trader $trader)
    {
        //if ($trader->----ПРОДАЖИ > 0) throw new \DomainException('Имеются документы, удалить нельзя');
        $trader->delete();
    }
}
