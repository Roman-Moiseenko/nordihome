<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;
use App\Modules\Admin\Entity\Admin;
use Illuminate\Console\Command;
class PasswordAdminCommand extends Command
{
    protected $signature = 'admin:password {--name= : "Логин для смены пароля"}';
    protected $description = 'Смена пароля Администратора сайта';

    public function handle()
    {
        $name = $this->option('name');
        /** @var Admin $admin */
        if ($admin = Admin::where('name', $name)->first()) {
            $password = $this->ask('Введите пароль');
            $admin->setPassword($password);
            $this->info('Пароль сменен Администратор!');
            return true;
        }
        $this->error('Администратор не найден ');


        return true;
    }
}
