<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Base\Entity\BankDetail;
use App\Modules\Base\Entity\CompanyContact;
use App\Modules\Base\Entity\CompanyDetail;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Product\Entity\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributorService
{

    private OrganizationService $service;
    private SupplyService $supplyService;

    public function __construct(OrganizationService $service, SupplyService $supplyService)
    {
        $this->service = $service;
        $this->supplyService = $supplyService;
    }

    public function create(Request $request): Distributor
    {
        DB::transaction(function () use ($request, &$distributor) {
            $distributor = Distributor::register(
                $request->string('name')->trim()->value(),
                $request->integer('currency'));

            $currency = Currency::find($request->integer('currency'));
            if (!$currency->default) {
                $distributor->foreign = true;
                $distributor->save();
            }

            $organization = $this->service->create_find(
                $request->string('inn')->trim()->value(),
                $request->string('bik')->trim()->value(),
                $request->string('account')->trim()->value()
            );
            $this->attach($distributor, $organization->id);
        });

        return $distributor;

        /*
        if (($organization_id = $request->integer('organization_id')) > 0) {
            $distributor->organization_id = $organization_id;
        } else {
            if (empty($request->input('inn'))) throw new \DomainException('Не выбрана организация, необходимо привязать компанию к поставщику');

            $organization = $this->service->create_find(
                $request->string('inn')->value(),
                $request->string('bik')->value(),
                $request->string('account')->value()
            );
            $distributor->organization_id = $organization->id;
        }
        $distributor->foreign = $request->boolean('foreign');
        $distributor->save();
        return $distributor;
        */
    }

    public function update(Request $request, Distributor $distributor): Distributor
    {
        $distributor->name = $request->string('name')->trim()->value();
        $distributor->currency_id = $request->integer('currency_id');
        $distributor->organization_id = $request->integer('organization_id');
        $distributor->foreign = $request->boolean('foreign');
        $distributor->save();
        return $distributor;
    }

    public function create_supply(Distributor $distributor, string $balance): SupplyDocument
    {
        DB::transaction(function () use ($distributor, $balance, &$supply) {
            //Создаем заказ со стеком
            $supply = $this->supplyService->createStack($distributor);
            //Добавляем список товаров
            foreach ($distributor->products as $product) {
                if (
                    ($balance == 'all') ||
                    ($balance == 'empty' && $product->getQuantity() == 0) ||
                    ($balance == 'min' && $product->isBalance())
                ) {
                    $count = ($product->balance->max) ?? $product->balance->min;
                    if ($count > $product->getQuantity()) $count -= $product->getQuantity();
                    $supply->addProduct($product, $count, $product->pivot->cost);
                }
            }
        });
        return $supply;
    }

    public function attach(Distributor $distributor, int $organization_id): void
    {
        foreach ($distributor->organizations as $organization) {
            if ($organization->id == $organization_id) throw new \DomainException('Организация уже назначена!');
        }
        $default = is_null($distributor->organization);
        $distributor->organizations()->attach($organization_id, ['default' => $default]);

    }

    public function detach(Distributor $distributor, int $organization_id): void
    {
        $distributor->organizations()->detach($organization_id);
    }

    public function default(Distributor $distributor, int $organization_id): void
    {
        foreach ($distributor->organizations as $organization) {
            $distributor->organizations()->updateExistingPivot($organization->id, ['default' => false]);
        }

        $distributor->organizations()->updateExistingPivot($organization_id, ['default' => true]);
    }


    private function save_fields(Request $request, Distributor $distributor): void
    {
        $distributor->saveCompany($request);
        $distributor->save();
    }


    public function destroy(Distributor $distributor): void
    {
        if ($distributor->arrivals()->count() > 0) throw new \DomainException('Имеются документы, удалить нельзя');
        $distributor->delete();
    }
}
