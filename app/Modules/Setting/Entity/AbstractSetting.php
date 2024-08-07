<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

abstract class AbstractSetting
{
    public function __construct(array $data)
    {
        foreach ($data as $field => $value) {
            if (gettype($this->$field) == 'boolean') {
                $this->$field = true;
            } elseif (gettype($this->$field) == 'integer') {
                $this->$field = (int)($value);
            } else {
                $this->$field = $value;
            }
        }
    }
    abstract public function view();
}
