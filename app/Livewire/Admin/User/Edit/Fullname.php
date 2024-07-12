<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\User\Entity\User;
use Livewire\Component;

class Fullname extends Component
{

    public User $user;
    public bool $edit;
    public string $surname;
    public string $firstname;
    public string $secondname;
    public string $route = '/';

    public bool $change = false;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;
        $this->surname = $user->fullname->surname;
        $this->firstname = $user->fullname->firstname;
        $this->secondname = $user->fullname->secondname;
        $this->route = route('admin.user.show', $user);
    }

    public function open_change()
    {
        $this->change = true;
    }

    public function save_change()
    {
        $this->user->fullname->surname = $this->surname;
        $this->user->fullname->firstname = $this->firstname;
        $this->user->fullname->secondname = $this->secondname;
        $this->user->save();
        $this->user->refresh();
        $this->change = false;
    }

    public function close_change()
    {
        $this->surname = $this->user->fullname->surname;
        $this->firstname = $this->user->fullname->firstname;
        $this->secondname = $this->user->fullname->secondname;
        $this->change = false;
    }

    public function render()
    {
        return view('livewire.admin.user.edit.fullname');
    }
}
