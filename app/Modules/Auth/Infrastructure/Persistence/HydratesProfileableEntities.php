<?php

namespace App\Modules\Auth\Infrastructure\Persistence;

use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\Gender;
use App\Modules\Auth\Domain\ValueObjects\PersonalDataConsent;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Auth\Infrastructure\Models\Staff;
use DateTimeImmutable;

trait HydratesProfileableEntities
{
    use HydratesUserEntity;

    /**
     * @throws \DateMalformedStringException
     */
    private function hydrateClient(Client $model): ClientEntity
    {
        $fullName = new FullName($model->full_name);

        $client = new ClientEntity(
            fullName: $fullName,
            email: new Email($model->email),
            phone: $model->phone ? new PhoneNumber($model->phone) : null,
        );

        $client->id = $model->id;

        if ($model->birth_date) {
            $client->birthDate = DateTimeImmutable::createFromMutable($model->birth_date);
        }
        if ($model->gender) {
            $client->gender = new Gender($model->gender);
        }

        if ($model->country || $model->city || $model->region) {
            $client->address = new Address(
                $model->country,
                $model->city,
                $model->street,
                $model->region,
                $model->postal_code
            );
        }

        if ($model->banned_at) {
            $client->bannedAt = DateTimeImmutable::createFromMutable($model->banned_at);
        }

        // Восстановление согласия
        if ($model->consented && $model->policy_version) {
            $client->dataConsent = new PersonalDataConsent(
                policyVersion: $model->policy_version,
                actionIdentifier: $model->action_identifier,
                active: $model->consent_active
            );

            if ($model->consented_at) {
                $client->dataConsent->consentedAt = DateTimeImmutable::createFromMutable($model->consented_at);
            }
        } else {
            $client->dataConsent = null;
        }

        $client->user = $this->hydrateUser($model->user);

        return $client;
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function hydrateStaff(Staff $model): StaffEntity
    {
        $fullName = new FullName($model->full_name);
        $positions = new StaffPositions($model->positions ?? []);
        $staff = new StaffEntity($fullName, $positions);
        $staff->id = $model->id;

        if ($model->department) $staff->department = $model->department;
        if ($model->work_phone) $staff->workPhone = new PhoneNumber($model->work_phone);
        if ($model->personal_phone) $staff->personalPhone = new PhoneNumber($model->personal_phone);
        if ($model->work_email) $staff->workEmail = new Email($model->work_email);

        if ($model->termination_date) {
            $staff->terminate(DateTimeImmutable::createFromMutable($model->termination_date));
        } else {
            $staff->terminationDate = null;
        }
        if ($model->birth_date) $staff->birthDate = DateTimeImmutable::createFromMutable($model->birth_date);
        if ($model->hire_date) $staff->hireDate = DateTimeImmutable::createFromMutable($model->hire_date);

        if ($model->telegram_chat_id) $staff->telegramChatId = $model->telegram_chat_id;
        if ($model->max_chat_id) $staff->maxChatId = $model->max_chat_id;
        if ($model->notes) $staff->notes = $model->notes;

        $staff->user = $this->hydrateUser($model->user);

        return $staff;
    }

}
