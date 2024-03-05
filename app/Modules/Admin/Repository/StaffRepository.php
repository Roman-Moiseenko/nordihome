<?php
declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use JetBrains\PhpStorm\ExpectedValues;

class StaffRepository
{
    public function getStaffsByCode(#[ExpectedValues(valuesFromClass: Responsibility::class)] int $code)
    {
        return Admin::where('role', Admin::ROLE_STAFF)->whereHas('responsibilities', function ($q) use ($code) {
            $q->where('code', $code);
        })->get();
    }
}
