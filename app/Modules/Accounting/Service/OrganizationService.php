<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\OrganizationContact;
use App\Modules\Accounting\Entity\OrganizationHolding;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use Illuminate\Http\Request;

class OrganizationService
{

    public function create(Request $request): Organization
    {
        $organization = Organization::register(
            $request->string('full_name')->value(),
            $request->string('short_name')->value(),
            $request->string('inn')->value());
        return $this->update($organization, $request);
    }

    public function create_find(string $inn, string $bik, string $pay_account): Organization
    {
        $token = env('DADATA_TOKEN');
        $secret = env('DADATA_KEY');
        $dadata = new \Dadata\DadataClient($token, $secret);

        //Компания
        $response = $dadata->findById("party", $inn);
        $data = $response[0]['data'];

        $organization = Organization::register(
            $data['name']['full_with_opf'],
            $data['name']['short_with_opf'],
            $inn);

        $organization->kpp = $data['kpp'] ?? '';
        $organization->ogrn = $data['ogrn'];
        $address = $data['address']['data'];
        $organization->legal_address = GeoAddress::create(
            address: $address['source'],
            post: $address['postal_code'],
            region: $address['region_with_type']
        );
        $organization->actual_address = GeoAddress::create(
            address: $address['source'],
            post: $address['postal_code'],
            region: $address['region_with_type']
        );

        $organization->post = $data['management']['post'];

        list($f1, $f2, $f3) = explode(' ', $data['management']['name']);
        $organization->chief = new FullName($f1, $f2, $f3);

        //Банк
        $response = $dadata->findById("bank", $bik);
        $data = $response[0]['data'];

        $organization->bik = $bik;
        $organization->bank_name = $data['name']['payment'];
        $organization->corr_account = $data['correspondent_account'];
        $organization->pay_account = $pay_account;

        $organization->save();
        return $organization;
    }

    public function update(Organization $organization, Request $request): Organization
    {

        $organization->full_name = $request['full_name'];
        $organization->short_name = $request['short_name'] ?? '';
        $organization->inn = $request['inn'] ?? '';

        $organization->kpp = $request['kpp'] ?? '';
        $organization->ogrn = $request['ogrn'] ?? '';
        $organization->legal_address = GeoAddress::create(
            params: $request->input('legal_address')
        );
        $organization->actual_address = GeoAddress::create(
            params: $request->input('actual_address')
        );

        $organization->bik = $request['bik'] ?? '';
        $organization->bank_name = $request['bank_name'] ?? '';
        $organization->corr_account = $request['corr_account'] ?? '';
        $organization->pay_account = $request['pay_account'] ?? '';

        $organization->email = $request['email'] ?? '';
        $organization->phone = $request['phone'] ?? '';
        $organization->post = $request['post'] ?? '';
        $organization->chief = FullName::create(
            params: $request->input('chief')
        );

        if (!empty($holding = $request['holding_id'])) {
            if (is_array($holding)) $holding = $holding[0]; //Если массив, берем первый элемент
            if (is_numeric($holding)) {
                $organization->holding_id = (int)$holding;
            } else {
                $holding = $this->createHolding($holding); //Создаем Холдинг
                $organization->holding_id = $holding->id;
            }
        } else {
            $organization->holding_id = null;
        }

        $organization->save();
        return $organization;
    }

    public function delete(Organization $organization)
    {
        if ($organization->isDefault()) throw new \DomainException('Нельзя удалить организацию по умолчанию');
        $organization->delete();
    }

    public function add_contact(Organization $organization, Request $request)
    {
        $contact = OrganizationContact::new(FullName::create(
            params: $request->input('fullname')
        ));

        $contact->phone = preg_replace(
            "/[^0-9]/", "",
            $request->string('phone')->value());
        $contact->email = $request->string('email')->value();
        $contact->post = $request->string('post')->value();
        $organization->contacts()->save($contact);

    }

    public function del_contact(OrganizationContact $contact)
    {
        $contact->delete();
    }

    public function set_contact(OrganizationContact $contact, Request $request)
    {


        //$contact = OrganizationContact::find($request->integer('contact_id'));
        $contact->fullname = FullName::create(
            params: $request->input('fullname')
        );
        $contact->phone = preg_replace("/[^0-9]/", "", $request->string('phone')->value());
        $contact->email = $request->string('email')->value();
        $contact->post = $request->string('post')->value();
        $contact->save();
    }

    private function createHolding(string $name): OrganizationHolding
    {
        $holding = OrganizationHolding::where('name', $name)->first();
        if (is_null($holding)) $holding = OrganizationHolding::register($name);
        return $holding;
    }


}
