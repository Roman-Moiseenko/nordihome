<?php

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\Actions\Auth\LoginUserUseCase;
use App\Modules\Auth\Application\Actions\Client\CreateClientUseCase;
use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\Actions\User\RegisterUserClientUseCase;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Auth\Application\DTOs\LoginData;
use App\Modules\Auth\Application\DTOs\User\RegisterUserData;
use App\Modules\Auth\Application\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class LoginOrRegisterUserService
{
    public function __construct(
        private UserRepositoryInterface   $userRepository,
        private RegisterUserClientUseCase $registerUserClientUseCase,
        private CreateClientUseCase       $createClientUseCase,
        private LoginUserUseCase          $loginUserUseCase,
        private FindClientByContactUseCase $findClientByContactUseCase,
    )
    {
    }

    public function execute(LoginData $dto): string
    {
        //Ищем user по email
        $user = $this->userRepository->findByEmail(new Email($dto->email));
        //Новый клиент
        if (is_null($user)) {

            $findDto = new FindClientByContactData(email: $dto->email);
            //Ищем клиента по email
            if (is_null($client = $this->findClientByContactUseCase->execute($findDto))) {
                //Регистрируем клиента
                $clientDto = new ClientCreateData(
                    lastName: '',
                    firstName: '',
                    email: $dto->email,
                );
                $client = $this->createClientUseCase->execute($clientDto, new UserPermission(null, ['role:admin'], ['auth.buyer.create']));
            }
            //Привязываем новый user к клиенту
            $registerDto = new RegisterUserData($dto->email, $dto->password);
            $this->registerUserClientUseCase->execute($client->id, $registerDto, new UserPermission());
            return 'verification';
        }

        if (!$user->isEmailVerified()) return 'verification'; //не верифицирован
        if ($user->isBanned) return 'banned'; // забанен

        //логинимся
        return $this->loginUserUseCase->execute($dto) ? 'login' : 'password';
    }

}
