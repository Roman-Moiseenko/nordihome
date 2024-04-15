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
           // $request['phone'],
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

    public function newUser($email, $password)
    {
        //$user = User::where($email)->first();
        $user = User::register(
            $email,
            // $request['phone'],
            $password
        );
        return $user;
    }
}
