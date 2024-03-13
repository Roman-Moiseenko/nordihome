<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\RegisterService;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->route('login');
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
        return Auth::guard('user');
    }


}
