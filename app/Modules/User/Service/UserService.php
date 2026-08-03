<?php


namespace App\Modules\User\Service;


use App\Mail\VerifyMail;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function setFullname(Client $client, Request $request): bool
    {
        $fullname = $request->string('fullname')->trim()->value();
        if ($fullname == $client->fullname->getFullName()) return false;
        list ($surname, $firstname, $secondname) = explode(" ", $fullname);
        $client->setNameField($surname, $firstname, $secondname);
        return true;
    }

    public function setPhone(Client $client, Request $request): bool
    {
        $client->setPhone(phoneToDB($request->string('phone')));
        return true;
    }

    public function setPassword(Client $client, Request $request): bool
    {
        $password = $request->string('password')->trim()->value();
        if (strlen($password) < 6) throw new \DomainException('Длина пароля должна быть не менее 6 символов');

        $client->setPassword($password);
        return true;
    }

    /**
     * Изменение почты с проверкой и верификацией, для изменения клиентом
     */
    public function setEmail(Client $client, Request $request): bool
    {
        $email = $request->string('email')->trim()->value();
        if ($email == $client->email) return false;
        if (!empty(User::where('email', $email)->first()))
            throw new \DomainException('Пользователь с таким email уже существует!');
        $client->email = $email;
      //  $client->active = false;
        $client->save();

        Mail::to($client->email)->send(new VerifyMail($client));
        Auth::logout();
        return true;
    }
}
