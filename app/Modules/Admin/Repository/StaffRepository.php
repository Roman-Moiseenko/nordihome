<?php
declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ExpectedValues;

class StaffRepository
{
    public function getStaffsByCode(#[ExpectedValues(valuesFromClass: Responsibility::class)] int $code, bool $chief = false): array
    {
        $query = Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($code) {
            $q->where('code', $code);
        });
        if ($chief) $query->orWhere('role', Admin::ROLE_CHIEF);
        return $query->getModels();
    }

    public function getStaffsByCodes(array $codes): array
    {
        return Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($codes) {
            $q->whereIn('code', $codes);
        })->getModels();
    }

    public function getChief(): array
    {
        return Admin::where('role', Admin::ROLE_CHIEF)->getModels();
    }

    public function getIndex(Request $request, &$filters):Arrayable
    {
        $query = Admin::orderByDesc('id');
        $filters = [];
        if (!empty($value = $request->get('role'))) {
            $filters['role'] = $value;
            $query->where('role', $value);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Admin $staff) => $this->StaffToArray($staff));
    }

    public function getStaffsChiefs(): array
    {
        return Admin::where('role', Admin::ROLE_STAFF)->orWhere('role', Admin::ROLE_CHIEF)->getModels();
    }

    private function StaffToArray(Admin $staff): array
    {
        return array_merge($staff->toArray(), [
            'photo' => $staff->getPhoto(),
            'role_name' => $staff->roleText(),
        ]);
    }
    public function StaffWithToArray(Admin $staff): array
    {
        return array_merge($this->StaffToArray($staff), [
            'responsibilities' => $staff->responsibilities()->get()->pluck('code')->toArray(),
            'show_responsibilities' => $staff->isStaff(),
        ]);
    }

}
