<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Queries;

use App\Modules\Auth\Application\DTOs\Client\ClientInfoWebData;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\Gender;
use App\Modules\Auth\Domain\ValueObjects\PersonalDataConsent;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Auth\Infrastructure\Persistence\Query\ClientQueryRepository;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use DateTimeImmutable;
use DomainException;

readonly class GetInfoWebClientQuery
{
    public function __construct(private ClientQueryRepository $repository)
    {
    }

    public function execute(int $clientId): ClientInfoWebData
    {
        $data = $this->repository->getInfoClient($clientId);

        if (!$data) {
            throw new DomainException("Клиент не найден: {$clientId}");
        }

        $fullName = new FullName(
            implode(' ', array_filter([
                $data->last_name,
                $data->first_name,
                $data->middle_name,
            ]))
        );

        $email = new Email($data->email);

        $loginEmail = isset($data->login_email)
            ? new Email($data->login_email)
            : $email;

        $priceType = new PriceType($data->price_type ?? PriceType::RETAIL);

        $phone = new PhoneNumber($data->phone);

        $address = new Address(
            country: $data->country ?? '',
            city: $data->city ?? '',
            street: $data->street ?? '',
            region: $data->region,
            postalCode: $data->postal_code,
            regionCode: $data->region_code,
        );

        $consent = null;
        if (!empty($data->consented) && !empty($data->policy_version)) {
            $consent = new PersonalDataConsent(
                policyVersion: $data->policy_version,
                actionIdentifier: $data->action_identifier,
                active: (bool)$data->consent_active,
            );
            if (!empty($data->consented_at)) {
                $consent->consentedAt = new DateTimeImmutable($data->consented_at);
            }
        }


        $gender = !empty($data->gender)
            ? new Gender($data->gender)
            : null;

        return new ClientInfoWebData(
            id: (int)$data->id,
            fullName: $fullName,
            email: $email,
            userId: (int)($data->user_id ?? 0),
            loginEmail: $loginEmail,
            priceType: $priceType,
            discount: (float)($data->discount ?? 0),
            phone: $phone,
            consent: $consent,
            address: $address,
            gender: $gender,
            isPickup: (bool)$data->is_pickup,
        );
    }
}
