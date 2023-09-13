<?php

namespace App\Console\Commands\Admin;

use App\Entity\Admin;
use Illuminate\Console\Command;

class CreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        if (Admin::where('name', $name)->first()) {
            $this->error('Администратор с таким логином уже существует ');
            return false;
        }
        $email = $this->ask('Укажите почту для аутентификации');
        $phone = $this->ask('Укажите телефон для аутентификации');
        $password = $this->ask('Введите пароль');

        $admin = Admin::register($name, $email, $phone, $password);
        $this->info('Пользователь ' . $name . ' создан');
        $admin->changeRole(Admin::ROLE_ADMIN);
        $this->info('Роль Администратора назначена!');
        return true;
    }
}
