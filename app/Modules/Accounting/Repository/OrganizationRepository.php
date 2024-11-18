<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Organization;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class OrganizationRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Organization::orderBy('short_name');

        $filters = [];


        if (($name = $request->string('name')->trim()->value()) != '') {
            $filters['name'] = $name;
            $query->where(function ($query) use ($name) {
                $query->whereRaw("LOWER(short_name) like LOWER('%$name%')")
                    ->orWhereRaw("LOWER(full_name) like LOWER('%$name%')")
                    ->orWhere('inn', 'like', "%$name%")
                    ->orWhere('email', 'like', "%$name%")
                    ->orWhere('phone', 'like', "%$name%")
                    ->orWhereHas('contacts', function ($query) use ($name) {
                        $query->where('email', 'like', "%$name%")
                            ->where('phone', 'like', "%$name%");
                    });
            });
        }
        if (($holding = $request->integer('holding')) > 0) {
            $filters['holding'] = $holding;
            $query->where('holding_id', $holding);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Organization $organization) => $this->OrganizationToArray($organization));
    }

    private function OrganizationToArray(Organization $organization): array
    {
        return array_merge($organization->toArray(), [
            'types' => $organization->types(),
            'holding' => $organization->holding,
            'contacts' => $organization->contacts,
        ]);
    }

    public function OrganizationWithToArray(Organization $organization): array
    {
        return array_merge(
            $this->OrganizationToArray($organization),
            [
                'trader' => $organization->trader,
                'shopper' => $organization->shopper,
                'distributor' => $organization->distributor
            ],
        );
    }
}
