<?php
declare(strict_types=1);

namespace App\Modules\User\Service;


use App\Events\UserHasCreated;
use App\Events\UserHasRegistered;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\UserRegister;
use App\Mail\VerifyMail;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\User\Entity\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use function event;

class RegisterService
{

    public function register(Request $request): void
    {
        $user = User::register(
            $request['email'],
            $request['password']
        );
        event(new UserHasCreated($user));
    }

    public function verify($id): void
    {
        $user = User::findOrFail($id);
        $user->verify();
        event(new UserHasRegistered($user));
    }

    public function create(Request $request): User
    {
        $user = User::new(
            $request->string('email')->value(),
            preg_replace("/[^0-9]/", "", $request->string('phone')->value())
        );
        $user->setNameField(
            $request->string('surname')->value(),
            $request->string('firstname')->value(),
            $request->string('secondname')->value(),
        );
        return $user;
    }
}
