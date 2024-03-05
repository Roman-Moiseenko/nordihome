<?php

namespace App\Http\Controllers\Auth;

use App\Entity\Admin;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    use ThrottlesLogins;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
/*
    public function showLoginForm(): View
    {
        return view('auth.login');
    } */

    public function showLoginForm(): View
    {
        return view('admin.login');
    }
    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return Response
     *
     * @throws ValidationException
     */
    /*
    public function login(LoginRequest $request)
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
       $authenticate = Auth::guard('user')->attempt(
            $request->only(['email', 'password']),
            $request->filled('remember')
        );
        if ($authenticate) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);

            $user = Auth::user(); //Auth::guard('user')->user();

            if ($user->status != User::STATUS_ACTIVE) {
                Auth::logout();
                flash('Не верифицирован', 'danger');
                return back();
            }
                return redirect()->intended(route('home'));
        }
        $this->incrementLoginAttempts($request);
        throw ValidationException::withMessages(['email' => [trans('auth.failed')]]);
    }*/

    public function login(Request $request)
    {

        $this->validate($request, [
            'name'   => 'required',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['name' => $request['name'], 'password' => $request['password']], $request->get('remember'))) {

            /** @var Admin $admin */
            $admin = $this->guard()->user();
            if ($admin->isBlocked()) {
                Auth::logout();
                flash('Ваш аккаунт заблокирован', 'danger');
                return back();
            }

            flash('Добро пожаловать ' . $admin->fullname->getFullName(), 'success');
            return redirect()->intended('/admin');
        }

        return back()->withInput($request->only('name', 'remember'));

    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        //if (Auth::guard('user')->check()) throw new \DomainException('Неверный тип пользователя'); //$this->guard('user')->logout();
        if (Auth::guard('admin')->check()) {
            $this->guard()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($response = $this->loggedOut($request)) {
                return $response;
            }

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect('/admin');
        }
    }

    /**
     * The user has logged out of the application.
     *
     * @param Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard('admin');
    }

    protected function authenticated(Request $request, $user)
    {
        /*
        if (!$user->status != User::STATUS_ACTIVE) {
            $this->guard('user')->logout();
            flash('Нет подтверждения', 'danger');
            return back();
        } */
        return redirect()->intended($this->redirectPath());
    }
    protected function username(): string
    {
        return 'name';
    }

}
