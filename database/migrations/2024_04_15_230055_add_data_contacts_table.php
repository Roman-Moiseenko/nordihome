<?php

use App\Modules\Page\Entity\Contact;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $contacts = [
            'phone' => [
                'name' => 'Позвонить по телефону',
                'icon' => 'fa-sharp fa-solid fa-circle-phone',
                'color' => '#000000',
                'url' => 'tel:+74012373730',
                'type' => 1,
            ],
            'telegram' => [
                'name' => 'Написать в телеграм',
                'icon' => 'fa-brands fa-telegram',
                'color' => '#000000',
                'url' => 'https://t.me/nordihome1',
                'type' => 2,
            ],
            'vk' => [
                'name' => 'Сообщество в ВК',
                'icon' => 'fa-brands fa-vk',
                'color' => '#000000',
                'url' => 'https://vk.com/nordihome',
                'type' => 3,
            ],
            'whatsapp' => [
                'name' => 'Написать в Ватцап',
                'icon' => 'fa-brands fa-whatsapp',
                'color' => '#000000',
                'url' => 'https://wa.me/+79062108505?text=Здравствуйте, я хочу мебель из Икеа!',
                'type' => 4,
            ],
        ];
        foreach ($contacts as $contact) {
            $model = Contact::register(
                name: $contact['name'],
                icon: $contact['icon'],
                color: $contact['color'],
                url: $contact['url'],
                type: (int)$contact['type']
            );
            $model->published = true;
            $model->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $contacts = Contact::get();
        foreach ($contacts as $contact) {
            $contact->delete();
        }
    }
};
