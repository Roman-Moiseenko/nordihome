<?php


namespace App\Modules\User\Service;


use App\Mail\VerifyMail;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function setFullname(User $user, Request $request): bool
    {
        $fullname = $request->string('fullname')->trim()->value();
        if ($fullname == $user->fullname->getFullName()) return false;
        list ($surname, $firstname, $secondname) = explode(" ", $fullname);
        $user->setNameField($surname, $firstname, $secondname);
        return true;
    }

    public function setPhone(User $user, Request $request): bool
    {
        $user->setPhone(phoneToDB($request->string('phone')));
        return true;
    }

    public function setPassword(User $user, Request $request): bool
    {
        $password = $request->string('password')->trim()->value();
        if (strlen($password) < 6) throw new \DomainException('Длина пароля должна быть не менее 6 символов');

        $user->setPassword($password);
        return true;
    }

    /**
     * Изменение почты с проверкой и верификацией, для изменения клиентом
     */
    public function setEmail(User $user, Request $request): bool
    {
        $email = $request->string('email')->trim()->value();
        if ($email == $user->email) return false;
        if (!empty(User::where('email', $email)->first()))
            throw new \DomainException('Пользователь с таким email уже существует!');
        $user->email = $email;
        $user->active = false;
        $user->save();

        Mail::to($user->email)->send(new VerifyMail($user));
        Auth::logout();
        return true;
    }
}
