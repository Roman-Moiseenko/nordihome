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
            } else {
                $this->$field = $value;
            }
        }
    }

    public function save()
    {

    }

    abstract public function view();
}
