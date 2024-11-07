<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

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
        $distributor = Distributor::register(
            $request->string('name')->trim()->value(),
            $request->integer('currency_id'));

        if (($organization_id = $request->integer('organization_id')) > 0) {
            $distributor->organization_id = $organization_id;
        } else {
            if (empty($request->input('inn'))) throw new \DomainException('Не выбрана организация, необходимо привязать компанию к поставщику');
            $organization = $this->service->create($request);
            $distributor->organization_id = $organization->id;
        }
        $distributor->save();
        return $distributor;
    }

    public function update(Request $request, Distributor $distributor): Distributor
    {
        $distributor->name = $request->string('name')->trim()->value();
        $distributor->currency_id = $request->integer('currency_id');
        $distributor->organization_id = $request->integer('organization_id');
        $distributor->save();
        return $distributor;
    }

    public function create_supply(Distributor $distributor, string $balance): SupplyDocument
    {
        DB::transaction(function () use ($distributor, $balance, &$supply) {
            //Создаем заказ со стеком
            $supply = $this->supplyService->create_stack($distributor);
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
