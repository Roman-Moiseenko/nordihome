<?php
declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ExpectedValues;

class StaffRepository
{
    public function getStaffsByCode(#[ExpectedValues(valuesFromClass: Responsibility::class)] int $code)
    {
        return Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($code) {
            $q->where('code', $code);
        })->get();
    }

    public function getStaffsByCodes(array $codes)
    {
        return Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($codes) {
            $q->whereIn('code', $codes);
        })->get();
    }

    public function getChief()
    {
        return Admin::where('role', Admin::ROLE_CHIEF)->get();
    }

    public function getIndex(Request $request)
    {
        $query = Admin::orderByDesc('id');
        if (!empty($value = $request->get('role'))) {
            $query->where('role', $value);
        }
        return $query;
    }

    public function getStaffsChiefs()
    {
        return Admin::where('role', Admin::ROLE_STAFF)->orWhere('role', Admin::ROLE_CHIEF)->getModels();
    }
}
