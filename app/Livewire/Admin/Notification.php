<?php

namespace App\Livewire\Admin;

use App\Modules\Admin\Entity\Admin;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class Notification extends Component
{
    /** @var Admin $staff */
    public mixed $staff;

    public int $count;
    public mixed $notifications = [];

    public bool $visible = false; //Показываем скрытое поле
    public bool $new_notify = false;//Поступили онлайн новые сообщения

    public function boot()
    {
        $this->staff = Auth::guard('admin')->user();
        $this->count = $this->staff->unreadNotifications()->count();
    }

    public function mount()
    {
        $this->refresh_fields();
    }

    public function refresh_fields()
    {
        $count2 = $this->count;
        if (
            $this->new_notify == false &&
            $this->count < $this->staff->unreadNotifications()->count()
        )
            $this->new_notify = true;
        if ($this->count < $this->staff->unreadNotifications()->count()) $this->dispatch('lucide-icons');
        $this->count = $this->staff->unreadNotifications()->count();
        $this->notifications = $this->staff->unreadNotifications()->getModels();
        $this->dispatch('lucide-icons', count1: $this->count, count2: $count2);
    }

    #[On('update-notifications')]
    public function update_out($id)
    {
        $this->refresh_fields();
    }


    public function toggle_visible()
    {
        $this->visible = !$this->visible;

        if ($this->visible) {
            $this->count = $this->staff->unreadNotifications()->count();
            $this->new_notify = false;
        }
    }

    #[On('close-notifications')]
    public function close_dropdown()
    {
        $this->visible = false;
    }

    public function render()
    {
        return view('livewire.admin.notification');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в Уведомлениях', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
