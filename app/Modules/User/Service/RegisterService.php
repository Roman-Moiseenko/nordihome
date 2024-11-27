<?php
declare(strict_types=1);

namespace App\Modules\User\Service;


use App\Events\UserHasCreated;
use App\Events\UserHasRegistered;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\UserRegister;
use App\Mail\VerifyMail;
use App\Modules\Accounting\Service\OrganizationService;
use App\Modules\Base\Entity\FileStorage;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\User\Entity\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use function event;

class RegisterService
{

    private OrganizationService $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }

    public function register(Request $request): void
    {
        $user = User::register(
            $request['email'],
            $request['password']
        );
        event(new UserHasCreated($user));
    }

    public function verifyAdmin($id): void
    {
        $user = User::findOrFail($id);
        $user->verify();
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
            phoneToDB($request->string('phone'))
          //  preg_replace("/[^0-9]/", "", $request->string('phone')->trim()->value())
        );

        $user->fullname = FullName::create(params: $request->input('fullname'));
        $user->save();
        if (!is_null($request->input('inn'))) {
            $organization = $this->service->create_find(
                $request->string('inn')->trim()->value(),
                $request->string('bik')->trim()->value(),
                $request->string('account')->trim()->value()
            );
            $this->attach($user, $organization->id);
        }
        return $user;
    }

    public function attach(User $user, int $organization_id): void
    {
        foreach ($user->organizations as $organization) {
            if ($organization->id == $organization_id) throw new \DomainException('Организация уже назначена!');
        }
        $default = is_null($user->organization);
        $user->organizations()->attach($organization_id, ['default' => $default]);
    }


    public function detach(User $user, int $organization_id): void
    {
        $user->organizations()->detach($organization_id);
    }

    public function default(User $user, int $organization_id): void
    {
        foreach ($user->organizations as $organization) {
            $user->organizations()->updateExistingPivot($organization->id, ['default' => false]);
        }

        $user->organizations()->updateExistingPivot($organization_id, ['default' => true]);
    }

    public function setInfo(User $user, Request $request): void
    {
        $user->setEmail($request->string('email')->trim()->value());
        $user->setPhone(phoneToDB($request->string('phone')));
        $user->fullname = FullName::create(params: $request->input('fullname'));
        $user->address = GeoAddress::create(params: $request->input('address'));
        $user->delivery = $request->input('delivery');
        $user->client = $request->integer('client');
        $user->save();
    }

    public function upload(User $user, Request $request)
    {
        $file = FileStorage::upload(
            $request->file('file'),
            $request->string('type')->value(),
            $request->string('title')->value(),
        );

        $user->files()->save($file);
    }
}
