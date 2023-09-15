<?php
declare(strict_types=1);

namespace App\UseCases\Admin;

use App\Entity\Admin;
use App\Entity\User\FullName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterService
{

    public function register(Request $request): Admin
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
        $admin->setRole($request['role']);

        //Фото
/*        $file = $request->file('photo');
        $destinationPath = 'uploads';
        $file->move($destinationPath, $file->getClientOriginalName());
        $admin->photo = $file;
*/
        $admin->save();
        return $admin;
    }

    public function setRole(string $role, Admin $admin): void
    {
        $admin->setRole($role);
        $admin->update();
    }

    public function setPassword(string $password, Admin $admin): void
    {
        //TODO Проверка на пароль
        $admin->update(['password' => Hash::make($password)]);
    }

    public function activate(Admin $admin): void
    {
        if (!$admin->isBlocked()) {
            throw new \DomainException('Сотрудник не заблокирован');
        }
        $admin->activated();
        $admin->update();
    }

    public function blocking(Admin $admin): void
    {
        //TODO Проверить на связанны данные,
        // если их нет, то удаляем Сотрудника


        //Проверка на себя,
        /** @var Admin $current */
        $current = Auth::guard('admin')->user();
        if ($current->id == $admin->id) {
            throw new \DomainException('Нельзя заблокировать самого себя');
        }
        if ($admin->isBlocked()) {
            throw new \DomainException('Сотрудник уже заблокирован');
        }
        $admin->blocked();
        $admin->update();
    }

    public function update(Request $request, Admin $admin): Admin
    {
        $admin->name = $request['name'];
        $admin->email = $request['email'];
        $admin->phone = $request['phone'];

        $admin->setFullName(new FullName(
            $request['surname'],
            $request['firstname'],
            $request['secondname']
        ));

        $admin->post = $request['post'];
        $admin->setRole($request['role']);
        if (!empty($request['photo'])) {
            $admin->setPhoto($request['photo']);
        }
        $admin->update();

        return $admin;
    }
}
