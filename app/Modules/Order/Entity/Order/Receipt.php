<?php

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $payment_id
 * @property string $status  "succeeded",
 * @property int $fiscal_document_number  "3986",
 * @property string $fiscal_storage_number  "9288000100115785",
 * @property string $fiscal_attribute "2617603921",
 * @property Carbon $registered_at  "2019-05-13T17:56:00.000+03:00",
 * @property string $fiscal_provider_id  "fd9e9404-eaca-4000-8ec9-dc228ead2345",
 * @property int $tax_system_code  1
 *
 * @property Carbon $created_at
 */
class Receipt extends Model
{
    public $timestamps = false;
    protected $fillable = [

    ];
}
