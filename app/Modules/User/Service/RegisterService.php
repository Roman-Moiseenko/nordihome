<?php
declare(strict_types=1);

namespace App\Modules\User\Service;


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
 /*   private Mailer $mailer;
    private Dispatcher $dispatcher;

    public function __construct(Mailer $mailer, Dispatcher $dispatcher)
    {

        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
    }
*/
    public function register(Request $request): void
    {
        $user = User::register(
            $request['email'],
           // $request['phone'],
            $request['password']
        );
        //TODO Отправка почты клиенту
        //$this->mailer->to($user->email)->send(new VerifyMail($user));
        Mail::to($user->email)->send(new VerifyMail($user));
        //$this->dispatcher->dispatch(new Registered($user));
        event(new Registered($user));
    }

    public function verify($id): void
    {
        $user = User::findOrFail($id);
        $user->verify();
        //TODO Верификация прошла
        // Письмо клиенту, + баллы на покупку (Coupon)
        $coupon = Coupon::register($user->id, 500, now(), now()->addHours(3));
        Mail::to($user->email)->queue(new UserRegister($user, $coupon));

        event($user);
        //
    }
}
