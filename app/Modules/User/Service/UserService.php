<?php


namespace App\Modules\User\Service;


use App\Entity\FullName;
use App\Mail\VerifyMail;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function setFullname(User $user, Request $request): bool
    {
        $fullname = trim($request['fullname']);
        if ($fullname == $user->delivery->fullname->getFullName()) return false;
        list ($surname, $firstname, $secondname) = explode(" ", $fullname);
        $user->delivery->setFullName(new FullName($surname, $firstname, $secondname));
        return true;
    }

    public function setPhone(User $user, Request $request): bool
    {
        $phone = trim($request['phone']);
        if ($phone == $user->phone) return false;
        $user->phone = $phone;
        $user->save();
        return true;
    }

    public function setPassword(User $user, Request $request): bool
    {
        $password = trim($request['password']);
        if (strlen($password) < 6) throw new \DomainException('Длина пароля должна быть не менее 6 символов');
        //TODO другие проверки на сложность пароля
        $user->setPassword($request['password']);
        return true;
    }

    public function setEmail(User $user, Request $request): bool
    {
        $email = trim($request['email']);
        if ($email == $user->email) return false;
        if (!empty(User::where('email', $email)->first()))
            throw new \DomainException('Пользователь с таким email уже существует!');
        $user->email = $email;
        $user->status = User::STATUS_WAIT;
        $user->save();
        //TODO Заменить на отправку ссылки для верификации
        Mail::to($user->email)->send(new VerifyMail($user));
        Auth::logout();
        return true;
    }
}
