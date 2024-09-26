<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use App\Modules\Base\Casts\BankDetailCast;
use App\Modules\Base\Casts\CompanyContactCast;
use App\Modules\Base\Casts\CompanyDetailCast;
use App\Modules\Base\Casts\GeoAddressCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property GeoAddress $legal_address
 * @property GeoAddress $actual_address
 * @property BankDetail $bank_detail
 * @property CompanyDetail $company_detail
 * @property CompanyContact $company_contact
 */
abstract class CompanyModel extends Model
{
    /**
     * Объединяем базовые параметры
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $casts = [
            'legal_address' => GeoAddressCast::class,
            'actual_address' => GeoAddressCast::class,
            'bank_detail' => BankDetailCast::class,
            'company_detail' => CompanyDetailCast::class,
            'company_contact' => CompanyContactCast::class,
        ];
        $fillable = [

        ];
        $attributes = [
            'legal_address' => '{}',
            'actual_address' => '{}',
            'bank_detail' => '{}',
            'company_detail' => '{}',
            'company_contact' => '{}',
        ];

        $this->casts = array_merge($this->casts, $casts);
        $this->fillable = array_merge($this->fillable, $fillable);
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function saveCompany(Request $request)
    {
        $this->company_detail = CompanyDetail::create(
            params: $request->all()
        );

        $this->legal_address = GeoAddress::create(
            params: $request->input('legal_address')
        );
        $this->actual_address = GeoAddress::create(
            params: $request->input('actual_address')
        );
        $this->bank_detail = BankDetail::create(
            params: $request->all()
        );
        $this->company_contact = CompanyContact::create(
            params: $request->all()
        );
    }
}
