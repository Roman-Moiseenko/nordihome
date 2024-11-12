<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;

use App\Modules\User\Entity\User;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;


class UserInfo extends Component
{


    public Order $order;
    public bool $edit = true;

    public string $phone;
    public string $email;
    public string $name;
    public int|null $user_id = null;
    public bool $show = false;
    public string $button_caption = 'Добавить и Выбрать';

    public function mount(Order $order, $edit = true)
    {
        $this->order = $order;
        $this->edit = $edit;
    }

    public function updatedPhone()
    {
        $phone = preg_replace('#\D#', '', $this->phone);

        $user = User::where('phone', $phone)->first();
        //dd([$phone, $user->id]);
        if (is_null($user)) {
            $this->button_caption = 'Добавить и Выбрать';
            $this->user_id = null;
        } else {

            $this->email = $user->email;
            $this->name = $user->fullname->firstname;
            $this->user_id = $user->id;
            $this->button_caption = 'Выбрать';
        }
    }

    public function updatedEmail()
    {
        $user = User::where('email', $this->email)->first();
        if (is_null($user)) {
            $this->button_caption = 'Добавить и Выбрать';
            $this->user_id = null;
        } else {
            $this->phone = $user->phone;
            $this->name = $user->fullname->firstname;
            $this->user_id = $user->id;
            $this->button_caption = 'Выбрать';
        }
    }

    public function set_user(): void
    {
        //dd($this->user_id);
        if (is_null($this->user_id)) {
            if (empty($this->email) || empty($this->phone)) throw new \DomainException('Не заполнены основные поля');
            $user = User::new($this->email, preg_replace('#\D#', '', $this->phone));

                /*
                User::make([
                'phone' => preg_replace('#\D#', '', $this->phone),
                'email' => $this->email,
                'password' => Str::random(24),
                'status' => User::STATUS_ACTIVE,
            ]); */
            $user->fullname->firstname = $this->name;
            $user->save();
            $user->refresh();
            $this->order->user_id = $user->id;
            $this->order->save();

        } else {

            $this->order->user_id = $this->user_id;
            $this->order->save();

        }
        $this->close_fields();
    }

    public function toggle_fields(): void
    {
        $this->show = !$this->show;
    }

    public function close_fields(): void
    {
        $this->show = false;
        $this->phone = '';
        $this->name = '';
        $this->email = '';
        $this->user_id = null;
    }

    public function render()
    {
        return view('livewire.admin.order.user-info');
    }

    public function exception($e, $stopPropagation): void
    {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
           // $this->refresh_fields();
        }
    }
}
