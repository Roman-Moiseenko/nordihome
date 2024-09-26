<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Base\Entity\BankDetail;
use App\Modules\Base\Entity\CompanyContact;
use App\Modules\Base\Entity\CompanyDetail;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Product\Entity\Product;

use Illuminate\Http\Request;

class DistributorService
{

    private OrganizationService $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
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

    private function save_fields(Request $request, Distributor $distributor)
    {
        $distributor->saveCompany($request);
        $distributor->save();
    }




    public function destroy(Distributor $distributor)
    {
        if ($distributor->arrivals()->count() > 0) throw new \DomainException('Имеются документы, удалить нельзя');
        $distributor->delete();
    }

    public function arrival(Distributor $distributor, int $product_id, float $cost)
    {
        /** @var Product $_product */
        $_product = Product::find($product_id);
        foreach ($distributor->products as $product) {
            if ($product->id == $_product->id) {
                $distributor->updateProduct($product, $cost);
                return;
            }
        }
        $distributor->addProduct($_product, $cost);
    }
}
