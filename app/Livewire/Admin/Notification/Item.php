<?php

namespace App\Livewire\Admin\Notification;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Livewire\Component;

class Item extends Component
{
    /** @var DatabaseNotification $notification */
    public mixed $notification = null;


    public function mount($notification)
    {
        $this->notification = $notification;
    }

    public function remove()
    {
        $this->notification->markAsRead();
        $this->dispatch('update-notifications', id: $this->notification->id);
    }

    public function follow()
    {
        $this->notification->markAsRead();
        $this->dispatch('update-notifications');

        if (!empty($this->notification->data['route'])) $this->redirect($this->notification->data['route']);
    }

    public function render()
    {
        return view('livewire.admin.notification.item');
    }
}
