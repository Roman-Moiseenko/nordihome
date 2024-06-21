<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Organization;

class OrganizationService
{

    public function create(array $request): Organization
    {
        $organization = Organization::register($request['name'], $request['short_name'], $request['INN']);
        return $this->update($organization, $request);
    }

    public function update(Organization $organization, array $request): Organization
    {

        $organization->name = $request['name'];
        $organization->short_name = $request['short_name'] ?? '';
        $organization->INN = $request['INN'] ?? '';

        $organization->KPP = $request['KPP'] ?? '';
        $organization->OGRN = $request['OGRN'] ?? '';
        $organization->address->post = $request['index'] ?? '';
        $organization->address->address = $request['address'] ?? '';

        $organization->BIK = $request['BIK'] ?? '';
        $organization->bank = $request['bank'] ?? '';
        $organization->corr_account = $request['corr_account'] ?? '';
        $organization->account = $request['account'] ?? '';

        $organization->email = $request['email'] ?? '';
        $organization->phone = $request['phone'] ?? '';
        $organization->post_chief = $request['post_chief'] ?? '';
        $organization->chief->surname = $request['surname'] ?? '';
        $organization->chief->firstname = $request['firstname'] ?? '';
        $organization->chief->secondname = $request['secondname'] ?? '';

        if (isset($request['default'])) {
            /** @var Organization $item */
            foreach (Organization::get() as $item) {
                $item->default = false;
                $item->save();
            }
            $organization->default = true;
        }

        $organization->save();
        return $organization;
    }

    public function delete(Organization $organization)
    {
        if ($organization->isDefault()) throw new \DomainException('Нельзя удалить организацию по умолчанию');
        $organization->delete();
    }
}
