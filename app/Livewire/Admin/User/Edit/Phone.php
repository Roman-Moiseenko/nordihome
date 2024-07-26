<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\User\Entity\User;
use Livewire\Component;

class Phone extends Component
{

    public User $user;
    public bool $edit;
    public string $phone;

    public bool $change = false;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;
        $this->phone = phone($user->phone);
    }

    public function open_change()
    {
        $this->change = true;
    }

    public function save_change()
    {
        $this->user->setPhone($this->phone);
        $this->user->save();
        $this->user->refresh();
        $this->change = false;
    }

    public function close_change()
    {
        $this->phone = phone($this->user->phone);
        $this->change = false;
    }

    public function render()
    {
        return view('livewire.admin.user.edit.phone');
    }
}
