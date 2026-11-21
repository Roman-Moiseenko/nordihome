<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Application\DTOs\Organization\FilterOrganizationIndexData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationContactData;
use App\Modules\Accounting\Domain\Entities\OrganizationContactEntity;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Accounting\Domain\ValueObjects\Geo;
use App\Modules\Accounting\Infrastructure\Models\Organization;
use App\Modules\Accounting\Infrastructure\Models\OrganizationContact;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Base\Entity\FileStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function getById(int $id): OrganizationEntity
    {
        return $this->hydrate(Organization::with('contacts')->findOrFail($id));
    }

    public function save(OrganizationEntity $organization): OrganizationEntity
    {
        $model = $organization->id
            ? Organization::findOrFail($organization->id)
            : new Organization();

        $model->full_name = $organization->fullName;
        $model->short_name = $organization->shortName;
        $model->inn = $organization->inn;
        $model->kpp = $organization->kpp;
        $model->ogrn = $organization->ogrn;
        $model->foreign = $organization->foreign;
        $model->bik = $organization->bik;
        $model->bank_name = $organization->bankName;
        $model->corr_account = $organization->corrAccount;
        $model->pay_account = $organization->payAccount;
        $model->vat_id = $organization->vatId;
        $model->email = $organization->email?->value ?? '';
        $model->phone = $organization->phone->getValue();
        $model->post = $organization->post;
        $model->chief = $organization->chief->getValue();
        $model->active = $organization->active;
        $model->legal_address = $organization->legalAddress;
        $model->actual_address = [
            'country' => $organization->actualAddress->country,
            'region' => $organization->actualAddress->region,
            'city' => $organization->actualAddress->city,
            'street' => $organization->actualAddress->street,
            'house' => $organization->actualAddress->house,
            'building' => $organization->actualAddress->building,
            'flat' => $organization->actualAddress->flat,
            'postalCode' => $organization->actualAddress->postalCode,
            'regionCode' => $organization->actualAddress->regionCode,
        ];
        $model->geo = $organization->geo->toArray();

        $model->save();

        return $this->hydrate($model->fresh()->load('contacts'));
    }

    public function delete(int $id): void
    {
        Organization::findOrFail($id)->delete();
    }

    public function findByInn(string $inn): ?OrganizationEntity
    {
        $model = Organization::with('contacts')->where('inn', $inn)->first();
        return $model ? $this->hydrate($model) : null;
    }

    public function search(string $value): array
    {
        return Organization::active()
            ->whereRaw("LOWER(short_name) like LOWER(?)", ["%{$value}%"])
            ->orWhere('inn', 'like', "%{$value}%")
            ->get()
            ->map(fn(Organization $model) => [
                'id' => $model->id,
                'short_name' => $model->short_name,
                'inn' => $model->inn,
            ])
            ->toArray();
    }

    public function filterPaginate(FilterOrganizationIndexData &$filter): LengthAwarePaginator
    {
        $query = Organization::orderBy('short_name');

        $filter->count = 0;

        if (!is_null($filter->name) && trim($filter->name) !== '') {
            $name = trim($filter->name);
            $query->where(function ($query) use ($name) {
                $query->whereRaw("LOWER(short_name) like LOWER(?)", ["%{$name}%"])
                    ->orWhereRaw("LOWER(full_name) like LOWER(?)", ["%{$name}%"])
                    ->orWhere('inn', 'like', "%{$name}%")
                    ->orWhere('email', 'like', "%{$name}%")
                    ->orWhere('phone', 'like', "%{$name}%");
            });
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->through(fn(Organization $model) => $this->hydrate($model));
    }

    public function saveContact(int $organizationId, OrganizationContactData $data): OrganizationContactEntity
    {
        $contact = ($data->id !== null)
            ? OrganizationContact::where('organization_id', $organizationId)->findOrFail($data->id)
            : new OrganizationContact(['organization_id' => $organizationId]);

        $contact->post = (string) ($data->post ?? '');
        $contact->phone = new PhoneNumber($data->phone)->getValue();
        $contact->email = (string) ($data->email ?? '');
        $contact->fullname = (string) new FullName((string) ($data->fullname ?? ''));
        $contact->save();

        return $this->hydrateContact($contact);
    }

    public function deleteContact(int $contactId): void
    {
        OrganizationContact::findOrFail($contactId)->delete();
    }

    public function getContracts(int $organizationId): array
    {
        return Organization::findOrFail($organizationId)
            ->contracts()
            ->get()
            ->map(fn(FileStorage $file) => $this->mapFile($file))
            ->toArray();
    }

    public function getDocuments(int $organizationId): array
    {
        return Organization::findOrFail($organizationId)
            ->documents()
            ->get()
            ->map(fn(FileStorage $file) => $this->mapFile($file))
            ->toArray();
    }

    public function attachFile(int $organizationId, UploadedFile $file, string $type, string $title = ''): void
    {
        $organization = Organization::findOrFail($organizationId);
        $fileStorage = FileStorage::upload($file, $type, $title);
        $organization->files()->save($fileStorage);
    }

    public function getTypes(int $organizationId): string
    {
        $organization = Organization::findOrFail($organizationId);

        $types = [];
        if ($organization->trader()->exists()) $types[] = 'Продавец';
        if ($organization->shopper()->exists()) $types[] = 'Покупатель';
        if ($organization->distributor()->exists()) $types[] = 'Поставщик';

        return implode(' | ', $types);
    }

    public function getTraderLink(int $organizationId): ?array
    {
        $trader = Organization::findOrFail($organizationId)->trader()->first();

        return $trader ? ['id' => $trader->id, 'name' => $trader->name] : null;
    }

    public function getDistributorLink(int $organizationId): ?array
    {
        $distributor = Organization::findOrFail($organizationId)->distributor()->first();

        return $distributor ? ['id' => $distributor->id, 'name' => $distributor->name] : null;
    }

    public function getShopperLink(int $organizationId): ?array
    {
        $shopper = Organization::findOrFail($organizationId)->shopper()->first();

        return $shopper ? ['id' => $shopper->id, 'fullname' => (string) $shopper->fullname] : null;
    }

    private function hydrate(Organization $model): OrganizationEntity
    {
        $entity = new OrganizationEntity(
            fullName: $model->full_name,
            shortName: $model->short_name,
            inn: $model->inn,
        );

        $entity->id = $model->id;
        $entity->kpp = (string) $model->kpp;
        $entity->ogrn = (string) $model->ogrn;
        $entity->foreign = (bool) $model->foreign;
        $entity->bik = (string) $model->bik;
        $entity->bankName = (string) $model->bank_name;
        $entity->corrAccount = (string) $model->corr_account;
        $entity->payAccount = (string) $model->pay_account;
        $entity->vatId = $model->vat_id;
        $entity->email = $this->hydrateEmail($model->email);
        $entity->phone = new PhoneNumber($model->phone);
        $entity->post = (string) $model->post;
        $entity->chief = new FullName((string) $model->chief);
        $entity->active = (bool) $model->active;
        $entity->legalAddress = (string) $model->legal_address;
        $entity->actualAddress = $this->hydrateAddress($model->actual_address);
        $entity->geo = Geo::fromArray($model->geo);

        if ($model->relationLoaded('contacts')) {
            $entity->contacts = $model->contacts
                ->map(fn(OrganizationContact $contact) => $this->hydrateContact($contact))
                ->toArray();
        }

        return $entity;
    }

    private function hydrateContact(OrganizationContact $contact): OrganizationContactEntity
    {
        $entity = new OrganizationContactEntity(new FullName((string) $contact->fullname));
        $entity->id = $contact->id;
        $entity->post = (string) $contact->post;
        $entity->phone = new PhoneNumber($contact->phone);
        $entity->email = $this->hydrateEmail($contact->email);

        return $entity;
    }

    private function hydrateEmail(?string $value): ?Email
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new Email($value);
    }

    private function hydrateAddress(?array $data): Address
    {
        $data = is_array($data) ? $data : [];

        return new Address(
            country: $data['country'] ?? null,
            city: $data['city'] ?? null,
            street: $data['street'] ?? null,
            region: $data['region'] ?? null,
            postalCode: $data['postalCode'] ?? null,
            regionCode: $data['regionCode'] ?? null,
            house: $data['house'] ?? null,
            building: $data['building'] ?? null,
            flat: $data['flat'] ?? null,
        );
    }

    private function mapFile(FileStorage $file): array
    {
        return [
            'id' => $file->id,
            'url' => $file->getUploadFile(),
            'title' => $file->title,
            'type' => $file->type,
            'file' => $file->file,
        ];
    }
}
