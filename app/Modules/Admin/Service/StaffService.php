<?php
declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    }

    public function blocking(Admin $admin): void
    {
        /** @var Admin $current */
        $current = Auth::guard('admin')->user();//Проверка на себя,
        if ($current->id == $admin->id) {
            throw new \DomainException('Нельзя заблокировать самого себя');
        }
        if ($admin->isBlocked()) {
            throw new \DomainException('Сотрудник уже заблокирован');
        }
        $admin->blocked();
    }

    public function update(Request $request, Admin $admin)
    {
        DB::transaction(function () use ($request, $admin){
            $admin->name = $request->string('name')->trim()->value();
            $admin->email = $request->string('email')->trim()->value();
            $admin->phone = preg_replace("/[^0-9]/", "", $request['phone']);

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
            }
            $this->photo($admin, $request->file('file'));
            $admin->save();
        });
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
