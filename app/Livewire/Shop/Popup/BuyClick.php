<?php

namespace App\Livewire\Shop\Popup;

use App\Modules\User\Entity\User;
use Livewire\Attributes\On;
use Livewire\Component;

class BuyClick extends Component
{
    public User|null $user;
    public int $product_id = 0;
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public $payment;
    public $delivery;

    public array $errors = [];

    public function mount(mixed $user): void
    {
        $this->user = $user;
        if (!is_null($user)) {
            $this->email = $user->email;
            $this->phone = $user->phone;
        }

    }

    public function buy_on_click(): void
    {
        $this->errors = [];
        if (empty($this->email)) $this->errors['email'] = 'Укажите вашу почту';
        if (empty($this->phone)) $this->errors['phone'] = 'Укажите номер телефона';


        if (empty($this->errors)) {

            //TODO Формируем заявку

            $this->dispatch('e-cart', product_id: $this->product_id, e_type: 'add', quantity: 1);
            $this->dispatch('e-cart', product_id: $this->product_id, e_type: 'purchase', quantity: 1);

        }
    }

    #[On('buy-click')]
    public function buy_click($id): void
    {
        $this->product_id = $id;
    }

    public function render()
    {
        return view('livewire.shop.popup.buy-click');
    }
}
