<?php

namespace App\Console\Commands\User;

use App\Entity\User;
use Illuminate\Console\Command;

class RoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установка Роли';

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        /** @var $user User */
        if (!$user = User::where('email', $email)->first()) {
            $this->error('Пользователь не найден ' . $email);
            return false;
        }
        try {
            $user->changeRole($role);
        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }
        $this->info('Роль установлена.');
        return true;
    }
}
