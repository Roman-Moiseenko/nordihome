<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

abstract class AbstractSetting
{
    public function __construct(array $data)
    {
        foreach ($data as $field => $value) {
            if (gettype($this->$field) == 'boolean') {
                $this->$field = (bool)$value;
            } elseif (gettype($this->$field) == 'integer') {
                $this->$field = (int)($value);
            } elseif (gettype($this->$field) == 'double') {
                $this->$field = (float)($value);
            } elseif (gettype($this->$field) == 'string') {
                $this->$field = (string)($value);
            } else {
                $this->$field = $value;
            }
        }
    }

    public static function create(array $data): static
    {
        $setting = new static([]);

        foreach ($data as $field => $value) {
            if (gettype($setting->$field) == 'boolean') {
                $setting->$field = (bool)$value;
            } elseif (gettype($setting->$field) == 'integer') {
                $setting->$field = (int)($value);
            } elseif (gettype($setting->$field) == 'double') {
                $setting->$field = (float)($value);
            } elseif (gettype($setting->$field) == 'string') {
                $setting->$field = (string)($value);
            } else {
                $setting->$field = $value;
            }
        }
       // $setting->save();
        return $setting;
    }

    final public function save(): void
    {
      /*  $slug = '';
        $setting = Setting::where('slug', $slug)->first();
        $setting->data = $this;
        $setting->save();*/
    }

   // abstract public function view();
}
