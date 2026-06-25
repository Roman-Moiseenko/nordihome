<?php

namespace App\Http\Middleware;

use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Base\Helpers\AdminMenu;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{

    public function __construct(private readonly StaffRepositoryInterface $staffRepository)
    {
    }
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        //dd(\Diglactic\Breadcrumbs\Breadcrumbs::view('breadcrumbs::json-ld')->getData()['breadcrumbs']);
        return array_merge(parent::share($request), [
            'errors' => function () use ($request) {
                // Inertia передаёт ошибки через сессию, но если их нет - пустой объект
                return $request->session()->get('errors')
                    ? $request->session()->get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'auth' => function () use ($request) {
                if (!$request->user()) {
                    return ['user' => null];
                }

                $staff = $this->staffRepository->findByUserId($request->user()->id);
                return [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'first_name' => $staff?->fullName->getFirstName(),
                        'last_name' => $staff?->fullName->getLastName(),
                        'phone' => $staff?->workPhone?->getValue(),
                       // 'post' => $request->user()->post,
                        'account' => [
                            'id' => $request->user()->id,
                            'name' => $request->user()->email,
                        ],
                        'is_admin' => $request->user()->hasRole('admin'),
                    ] : null,
                ];
            },
            'menus' => function () use ($request) {
                return AdminMenu::menu();
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'info' => $request->session()->get('info'),
                    'warning' => $request->session()->get('warning'),
                ];
            },
            'breadcrumbs' => function () use($request) {
                if ($request->path() == 'admin/login') return '';
                return \Diglactic\Breadcrumbs\Breadcrumbs::view('breadcrumbs::json-ld')->getData()['breadcrumbs'];
            },
        ]);
    }
}
