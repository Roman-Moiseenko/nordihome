<?php
declare(strict_types=1);

namespace Entity\Admin;

use App\Entity\Admin;
use App\Entity\User\FullName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;


    public function testRegister(): void
    {
        $admin = Admin::register(
            $name = 'name',
            $email = 'email',
            $phone = 'phone',
            $password = 'password',
    );
        self::assertNotEmpty($admin);
        self::assertEquals($name, $admin->name);
        self::assertEquals($email, $admin->email);
        self::assertEquals($phone, $admin->phone);
        self::assertNotEmpty($admin->password);
        self::assertNotEquals($password, $admin->password);

        self::assertTrue($admin->isCommodity());
        self::assertFalse($admin->isBlocked());
    }

    public function testBlocked(): void
    {
        $admin = Admin::register(
            'name',
            'email',
            'phone',
            'password',
        );
        self::assertFalse($admin->isBlocked());
        $admin->blocked();
        self::assertTrue($admin->isBlocked());
    }

    public function testFullName(): void
    {
        $admin = Admin::new('name', 'email', 'phone', 'password');
        $fullname = new FullName('surname', 'firstname', 'secondname');
        $admin->setFullname($fullname); //запись в базу
        $admin->save();
        self::assertEquals($fullname->firstname, $admin->fullname_firstname);
        self::assertEquals($fullname->surname, $admin->fullname_surname);
        self::assertEquals($fullname->secondname, $admin->fullname_secondname);
    }

}
