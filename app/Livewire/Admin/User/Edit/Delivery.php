<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\User\Entity\User;
use Livewire\Component;

class Delivery extends Component
{
    public User $user;
    public bool $edit;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;
    }

    public function render()
    {
        return view('livewire.admin.user.edit.delivery');
    }
}
