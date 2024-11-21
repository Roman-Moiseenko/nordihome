<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\Accounting\Service\OrganizationService;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Livewire\Component;
use App\Modules\Accounting\Entity\Organization as OrganizationEntity;

class Organization extends Component
{
    public User $user;
    public bool $edit;
    public string $name;
    public string $inn;
    public string $bik;
    public string $pay_account;
    public bool $change = false;
    public string $route;
    public ?int $organization_id = null;

    public function mount(User $user, $edit = true): void
    {
        $this->user = $user;
        $this->edit = $edit;
        $this->refresh_fields();
    }

    private function refresh_fields(): void
    {
        $user = $this->user;
        /*
        if (!is_null($user->organization_id)) {
            $this->organization_id = $user->organization_id;
            $this->name = $user->organization->short_name;
            $this->inn = $user->organization->inn;
            $this->bik = $user->organization->bik;
            $this->pay_account = $user->organization->pay_account;
            $this->route = route('admin.accounting.organization.show', $user->organization);
        }
        */
    }

    public function open_change(): void
    {
        $this->change = true;
    }

    /**
     * @throws BindingResolutionException
     */
    public function save_change(): void
    {
        if (empty($this->inn)) {
            $this->user->organization_id = null;
            $this->organization_id = null;
            $this->name = '';
        } else {
            if (!is_null($this->organization_id)) {//Был найден в базе,
                $this->user->organization_id = $this->organization_id;
            } else {// если нет, то ищем на dadata, сохраняем в базе и выбираем
                $service = app()->make(OrganizationService::class);
                $organization = $service->create_find($this->inn, $this->bik, $this->pay_account);
              //  $this->organization_id = $organization->id;
             //   $this->name = $organization->short_name;
                $this->user->organization_id = $organization->id;
            }
        }
        $this->user->save();
        $this->user->refresh();
        $this->change = false;
        $this->refresh_fields();
    }

    public function find_inn()
    {
        $organization = OrganizationEntity::where('inn', $this->inn)->first();
        if (!is_null($organization)) {
            $this->bik = $organization->bik;
            $this->pay_account = $organization->pay_account;
            $this->name = $organization->short_name;
            $this->organization_id = $organization->id;
        } else {
            $this->bik = '';
            $this->pay_account = '';
            $this->name = '';
            $this->organization_id = null;
        }
    }

    public function close_change(): void
    {
        $this->refresh_fields();
        $this->change = false;
    }
    public function render()
    {
        return view('livewire.admin.user.edit.organization');
    }
}
