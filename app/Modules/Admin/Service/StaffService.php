<?php
declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Entity\FullName;
use App\Entity\Photo;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    public function register(Request $request): Admin
    {
        //Основные поля
        $admin = Admin::new(
            $request['name'],
            $request['email'] ?? '',
            $request['phone'],
            $request['password']
        );

        //ФИО
        $admin->setFullName(new FullName(
            $request['surname'],
            $request['firstname'],
            $request['secondname']
        ));

        $admin->telegram_user_id = $request['chat_id'] ?? null;
        //Должность и Роли
        $admin->post = $request['post'];
        $admin->setRole($request['role']);
        $admin->save();

        //Фото
        $this->photo($admin, $request->file('file'));
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

        /** @var \App\Modules\Admin\Entity\Admin $current */
        $current = Auth::guard('admin')->user();//Проверка на себя,
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
        $admin->email = $request['email'] ?? '';
        $admin->phone = $request['phone'];

        $admin->setFullName(new FullName(
            $request['surname'],
            $request['firstname'],
            $request['secondname']
        ));
        $admin->telegram_user_id = $request['chat_id'] ?? null;

        $admin->post = $request['post'];
        if (!$admin->isCurrent()) $admin->setRole($request['role']);
        $admin->update();

        //Фото

        if ($request['image-clear'] == 'delete') {
            $admin->photo->delete();
            //$admin->save();
        }

        $this->photo($admin, $request->file('file'));

        $admin->save();
        $admin->refresh();
        return $admin;
    }

    public function setResponsibility(Request $request, Admin $admin)
    {
        if (!$admin->isStaff()) throw new \DomainException('Обязанность назначается только персоналу');
        $admin->responsibilities()->delete();
        $responses = $request['response'];
        foreach ($responses as $respons) {
            $admin->responsibilities()->save(Responsibility::new((int)$respons));
        }
    }

    public function responsibility(int $code, Admin $admin)
    {
        if (!$admin->isStaff()) throw new \DomainException('Обязанность назначается только персоналу');

        $admin->toggleResponsibilities($code);
    }

    public function photo(Admin $admin, $file): void
    {
        if (empty($file)) return;
        if (!empty($admin->photo)) {
            $admin->photo->newUploadFile($file);
        } else {
            $admin->photo()->save(Photo::upload($file));
        }
        $admin->refresh();
    }

    public function delete(Admin $admin): void
    {
        throw new \DomainException('Нельзя удалить сотрудника! Данная функция доступна только Администратору!');
    }
}
