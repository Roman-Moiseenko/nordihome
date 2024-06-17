<?php

namespace App\Livewire\Shop\Widget;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Feedback extends Component
{
    public bool $send = false;
    public string $email = '';
    public string $phone = '';
    public string $text = '';
    public bool $check = false;

    public function boot()
    {
        //TODO Параметры конфигурации - куда отправлять, контактные данные и др.
    }

    public function send_message()
    {
        if ($this->check && !empty($this->text) && (!empty($this->email) || !empty($this->phone))) {
            Mail::to('saint_johnny@mail.ru')->queue(new \App\Mail\FeedBack($this->email, $this->phone, $this->text));
            $this->send = true;
        }
    }

    public function render()
    {
        return view('livewire.shop.widget.feedback');
    }
}
