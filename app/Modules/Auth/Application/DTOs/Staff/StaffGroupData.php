<?php

namespace App\Modules\Auth\Application\DTOs\Staff;

use App\Modules\Auth\Domain\Entities\StaffEntity;
use Spatie\LaravelData\Data;

/**
 * DTO для сотрудника в сгруппированном по должностям списке
 */
class StaffGroupData extends Data
{
    public function __construct(
        public int    $id,
        public string $fullName,
    )
    {
    }

    public static function fromEntity(StaffEntity $staff): self
    {
        return new self(
            $staff->id,
            $staff->fullName->getInitials(),
        );
    }
}
