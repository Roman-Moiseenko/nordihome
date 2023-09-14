<?php
declare(strict_types=1);

namespace App\UseCases\Admin;

use App\Entity\Admin;
use App\Entity\User\FullName;
use Illuminate\Http\Request;

class RegisterService
{

    public function register(Request $request)
    {
        //Основные поля
        $admin = Admin::new(
            $request['name'],
            $request['email'],
            $request['phone'],
            $request['password']
        );
        //ФИО
        $admin->setFullName(new FullName(
            $request['surname'],
            $request['firstname'],
            $request['secondname']
        ));

        //Должность и Роли
        $admin->post = $request['post'];
        $admin->changeRole($request['role']);

        //Фото
        $admin->setPhoto($request['photo']);

        $admin->save();
    }

    public function setRole()
    {

    }

    public function blocking($id)
    {

    }
}
