<?php
declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entity\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerifyMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

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
    public function register(RegisterRequest $request): void
    {
        $user = User::register(
            $request['name'],
            $request['email'],
            $request['password']
        );
        //$this->mailer->to($user->email)->send(new VerifyMail($user));
        //Mail::to($user->email)->send(new VerifyMail($user));
        //$this->dispatcher->dispatch(new Registered($user));
        event(new Registered($user));
    }

    public function verify($id): void
    {
        $user = User::findOrFail($id);
        $user->verify();

        //
    }
}
