<?php

namespace App\Console\Commands\User;


use App\Modules\User\Entity\User;
use App\Modules\User\Service\RegisterService;
use Illuminate\Console\Command;

class VerifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Верификация Пользователя по  email';
    private RegisterService $service;

    public function __construct(RegisterService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        if (!$user = User::where('email', $email)->first()) {
            $this->error('No find ' . $email);
            return false;
        }
        try {
            $this->service->verify($user->id);
        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }
        $this->info('Success! ' . $email);
        return true;
    }

}
