<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerifyMail;
use App\Providers\RouteServiceProvider;
use App\Entity\User;
use App\UseCases\Auth\RegisterService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    private RegisterService $service;


    public function __construct(RegisterService $service)
    {
        $this->middleware('guest');
        $this->service = $service;
    }

    public function verify($token)
    {
        if (!$user = User::where('verify_token', $token)->first()) {
            flash('Ошибка верификации', 'danger');
            //flash()->overlay()
            return redirect()->route('login');//->with('success', );
        }
        try {
            $this->service->verify($user->id);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'warning');
            return redirect()->route('login');
        }

        flash('Успех', 'success');
        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(RegisterRequest $request)
    {
        //$service = new RegisterService();
        $this->service->register($request);

        flash('Подтвердите почту', 'success');
        return redirect()->route('login');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


}
