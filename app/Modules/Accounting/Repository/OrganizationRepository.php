<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Base\Entity\FileStorage;
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
                'distributor' => $organization->distributor,
                'contracts' => ($organization->contracts()->count() == 0)
                    ? []
                    : $organization->contracts()->get()->map(function (FileStorage $file) {
                        return [
                            'id' => $file->id,
                            'url' => $file->getUploadFile(),
                            'title' => $file->title,
                            'type' => $file->type,
                            'file' => $file->file,
                        ];
                    }),
                'documents' => ($organization->documents()->count() == 0)
                    ? []
                    : $organization->documents()->get()->map(function (FileStorage $file) {
                        return [
                            'id' => $file->id,
                            'url' => $file->getUploadFile(),
                            'title' => $file->title,
                            'type' => $file->type,
                            'file' => $file->file,
                        ];
                    }),
            ],
        );
    }

    public function search(string $value)
    {
        return Organization::active()
            ->whereRaw("LOWER(short_name) like LOWER('%$value%')")
            ->orWhere('inn', 'like', "%$value%")
            ->get()->map(function (Organization $organization) {
                return [
                    'id' => $organization->id,
                    'short_name' => $organization->short_name,
                    'inn' => $organization->inn,
                ];
            });

    }

    public function getCustomers(): Arrayable
    {
        return Organization::has('trader')->get()->map(function (Organization $organization) {
            return [
                'id' => $organization->id,
                'short_name' => $organization->short_name,
                'inn' => $organization->inn,
            ];
        });
    }
}
