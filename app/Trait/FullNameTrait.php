<?php

namespace App\Trait;

use App\Entity\User\FullName;

/**
 * @property string $fullname_surname
 * @property string $fullname_firstname
 * @property string $fullname_secondname
 */
trait FullNameTrait
{
    public FullName $fullName;

    public function setFullName(FullName $fullName): void
    {
        $this->fullName = $fullName;
    }

    public static function boot()
    {
        parent::boot();
        self::saving(function ($admin) {
            $admin->fullname_surname = $admin->fullName->surname;
            $admin->fullname_firstname = $admin->fullName->firstname;
            $admin->fullname_secondname = $admin->fullName->secondname;
        });

        self::retrieved(function ($admin) {
            $admin->setFullName(new FullName($admin->fullname_surname, $admin->fullname_firstname, $admin->fullname_secondname));
        });
    }
}
