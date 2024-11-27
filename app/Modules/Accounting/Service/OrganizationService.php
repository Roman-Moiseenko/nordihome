<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\OrganizationContact;
use App\Modules\Accounting\Entity\OrganizationHolding;
use App\Modules\Base\Entity\FileStorage;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use Dadata\DadataClient;
use Illuminate\Http\Request;

class OrganizationService
{
    public function create_foreign(Request $request): Organization
    {
        if (!is_null($organization = Organization::where('inn', $request->string('inn')->value())->first())) return $organization;

        $organization = Organization::register(
            $request->string('name')->value(),
            $request->string('name')->value(),
            $request->string('inn')->value(),
            );
        $organization->bik = $request->string('bik')->value();
        $organization->bank_name = $request->string('bank')->value();
        $organization->pay_account = $request->string('account')->value();
        $organization->foreign = true;
        $organization->save();
        return $organization;
    }

    public function create_find(string $inn, string $bik, string $pay_account):? Organization
    {
        if (!is_null($organization = Organization::where('inn', $inn)->first())) return $organization;

        $dadata = $this->dadata();
        //Компания
        $response = $dadata->findById("party", $inn);
        if (empty($response)) throw new \DomainException('Неверный ИНН. Организация не найдена');
        $data = $response[0]['data'];

        $organization = Organization::register(
            $data['name']['full_with_opf'],
            $data['name']['short_with_opf'],
            $inn);

        $this->setDataInn($organization, $data);

        //Банк
        $response = $dadata->findById("bank", $bik);
        if (!empty($response)) {
            $data = $response[0]['data'];
            $this->setDataBank($organization, $data);
        }
        $organization->pay_account = $pay_account;
        $organization->save();
        return $organization;
    }

    public function update_find(Organization $organization): void
    {
        if ($organization->isForeign()) throw new \DomainException('Для иностранной компании данные не обновляются!');
        $dadata = $this->dadata();
        //Компания
        $response = $dadata->findById("party", $organization->inn);
        $data = $response[0]['data'];
        $this->setDataInn($organization, $data);
    }

    public function setContact(Organization $organization, Request $request): void
    {
        if (($id = $request->integer('id')) > 0) {
            $contact = $organization->getContactById($id);
            $contact->fullname = FullName::create(params: $request->input('fullname'));
            $contact->phone = preg_replace("/[^0-9]/", "", $request->string('phone')->value());
            $contact->email = $request->string('email')->value();
            $contact->post = $request->string('post')->value();
            $contact->save();

        } else {
            $contact = OrganizationContact::new(FullName::create(params: $request->input('fullname')));
            $contact->phone = preg_replace("/[^0-9]/", "", $request->string('phone')->value());
            $contact->email = $request->string('email')->value();
            $contact->post = $request->string('post')->value();
            $organization->contacts()->save($contact);
        }
    }

    private function createHolding(string $name): OrganizationHolding
    {
        $holding = OrganizationHolding::where('name', $name)->first();
        if (is_null($holding)) $holding = OrganizationHolding::register($name);
        return $holding;
    }

    private function setDataInn(Organization $organization, array $data): void
    {
        $organization->kpp = $data['kpp'] ?? '';
        $organization->ogrn = $data['ogrn'];
        $address = $data['address']['data'];
        $organization->legal_address = GeoAddress::create(
            address: $address['source'],
            post: $address['postal_code'],
            region: $address['region_with_type']
        );
        if ($organization->actual_address->address == '')
            $organization->actual_address = GeoAddress::create(
                address: $address['source'],
                post: $address['postal_code'],
                region: $address['region_with_type']
            );

        //if ($organization->post == '' || $organization->chief->surname == '') {
            if ($data['type'] == "INDIVIDUAL") {
                $organization->post = 'Индивидуальный предприниматель';
                $organization->chief = new FullName(
                    $data['fio']['surname'],
                    $data['fio']['name'],
                    $data['fio']['patronymic']
                );
            } else {
                $organization->post = $data['management']['post'];
                list($f1, $f2, $f3) = explode(' ', $data['management']['name']);
                $organization->chief = new FullName($f1, $f2, $f3);
            }
        //}
        $organization->save();
    }

    private function setDataBank(Organization $organization, array $data): void
    {
        $organization->bik = $data['bic'];
        $organization->bank_name = $data['name']['payment'];
        $organization->corr_account = $data['correspondent_account'];
        $organization->save();
    }

    public function setInfo(Organization $organization, Request $request): void
    {
        if ($request->has('pay_account')) $organization->pay_account = $request->string('pay_account')->value();
        if ($request->has('bik')) {
            if ($organization->isForeign()) {
                $organization->bik = $request->string('bik')->value();
            } else {
                $dadata = $this->dadata();
                $response = $dadata->findById("bank", $request->string('bik')->trim()->value());
                if (empty($response)) throw new \DomainException('Неверный БИК');
                $data = $response[0]['data'];
                $this->setDataBank($organization, $data);
            }
        }
        if ($request->has('bank')) $organization->bank_name = $request->string('bank')->value();
        if ($request->has('address')) {
            $address = $request->input('address');
            $organization->actual_address = GeoAddress::create(
                address: $address['address'],
                post: $address['post'],
                region: $address['region']
            );
        }

        if ($request->has('post')) $organization->post = $request->string('post')->value();
        if ($request->has('email')) $organization->email = $request->string('email')->value();
        if ($request->has('phone')) $organization->phone = $request->string('phone')->value();
        if ($request->has('chief')) $organization->chief = FullName::create(
            params: $request->input('chief')
        );

        if ($request->has('short_name')) $organization->short_name = $request->string('short_name')->value();
        if ($request->has('full_name')) $organization->full_name = $request->string('full_name')->value();

        if ($request->has('holding_id')) {
            $holding_id = $request->input('holding_id');
            if ($holding_id == null || is_numeric($holding_id)) $organization->holding_id = $holding_id;
            if (is_string($holding_id)) {
                $holding = $this->createHolding($holding_id);
                $organization->holding_id = $holding->id;
            }
        }

        $organization->save();
    }

    private function dadata(): DadataClient
    {
        $token = env('DADATA_TOKEN', '');
        $secret = env('DADATA_KEY', '');

        return new DadataClient($token, $secret);
    }

    public function upload(Organization $organization, Request $request): void
    {
        $file = FileStorage::upload(
            $request->file('file'),
            $request->string('type')->value(),
            $request->string('title')->value(),
        );

        $organization->files()->save($file);
    }

}
