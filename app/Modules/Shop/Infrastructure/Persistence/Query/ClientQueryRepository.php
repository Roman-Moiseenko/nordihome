<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Auth\Infrastructure\Models\Client;
use Illuminate\Support\Facades\DB;
use stdClass;

class ClientQueryRepository
{
    /**
     * Возвращает сырые данные клиента одним запросом:
     * - основные поля clients
     * - связанные users (id, email для входа)
     * - IDs товаров в избранном (wishes)
     */
    public function getInfoClient(int $clientId): ?stdClass
    {
        return DB::table('clients as c')
            ->leftJoin('users as u', function ($join) {
                $join->on('u.profileable_id', '=', 'c.id')
                    ->where('u.profileable_type', '=', Client::class);
            })
            ->leftJoin('wishes as w', 'w.client_id', '=', 'c.id')
            ->where('c.id', $clientId)
            ->select(
                'c.id',
                'c.gender',
                'c.last_name',
                'c.first_name',
                'c.middle_name',
                'c.email',
                'c.phone',
                'c.country',
                'c.region',
                'c.region_code',
                'c.city',
                'c.street',
                'c.postal_code',
                'c.price_type',
                'c.discount',
                'c.consented',
                'c.consented_at',
                'c.policy_version',
                'c.action_identifier',
                'c.consent_active',
                'u.id as user_id',
                'u.email as login_email',
                DB::raw('GROUP_CONCAT(w.product_id ORDER BY w.product_id) as wishes_ids')
            )
            ->groupBy('c.id', 'u.id')
            ->first();
    }
}
