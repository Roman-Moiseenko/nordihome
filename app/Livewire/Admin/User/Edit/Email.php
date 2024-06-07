<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\User\Entity\User;
use Livewire\Component;

class Email extends Component
{

    public User $user;
    public bool $edit;
    public string $email;

    public bool $change = false;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;
        $this->email = $user->email;
    }

    public function open_change()
    {
        $this->change = true;
    }

    public function save_change()
    {
        $this->user->email = $this->email;
        $this->user->save();
        $this->user->refresh();
        $this->change = false;
    }

    public function close_change()
    {
        $this->email = $this->user->email;
        $this->change = false;
    }

    public function render()
    {
        return view('livewire.admin.user.edit.email');
    }
}
