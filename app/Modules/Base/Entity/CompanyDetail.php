<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class CompanyDetail
{
    public string $short_name;
    public string $full_name;
    public string $inn;
    public string $kpp;
    public string $ogrn;


    public function __construct()
    {
        $this->short_name = '';
        $this->full_name = '';
        $this->inn = '';
        $this->kpp = '';
        $this->ogrn = '';
    }

    public static function create(
        string $short_name = '',
        string $full_name = '',
        string $inn = '',
        string $kpp = '',
        string $ogrn = '',
        array $params = [])
    : self
    {
        $company = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (isset($company->$key))
                    $company->$key = $value ?? '';
            }
        } else {
            $company->short_name = $short_name;
            $company->full_name = $full_name;
            $company->inn = $inn;
            $company->kpp = $kpp;
            $company->ogrn = $ogrn;

        }
        return $company;
    }


    public static function fromArray(?array $params)
    {
        $company = new static();

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $company->$key = $value ?? '';
            }
        }

        return $company;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }

}

