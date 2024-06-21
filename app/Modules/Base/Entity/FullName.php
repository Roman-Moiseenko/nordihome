<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;



class FullName
{
    public string $surname;
    public string $firstname;
    public string $secondname;

    public function __construct($surname = '', $firstname = '', $secondname = '')
    {
        $this->surname = $surname;
        $this->firstname = $firstname;
        $this->secondname = $secondname;
    }

    public function getFullName(): string
    {
        return  $this->surname . ' ' . $this->firstname . (!empty($this->secondname) ? ' ' . $this->secondname : '') ;
    }

    public function getShortname(): string
    {
        return  $this->surname . ' ' . mb_substr($this->firstname, 0, 1) . '.' . (!empty($this->secondname) ? '' . mb_substr($this->secondname, 0, 1) . '.' : '') ;
    }

    public function isEmpty(): bool
    {
        return empty($this->surname);
    }

    public static function fromArray(?array $params): self
    {
        $full = new static();
        if (!empty($params)) {
            $full->surname = $params['surname'];
            $full->firstname = $params['firstname'];
            $full->secondname = $params['secondname'];
        }
        return $full;
    }

    public function toArray(): array
    {
        return [
            'surname' => $this->surname,
            'firstname' => $this->firstname,
            'secondname' => $this->secondname,
        ];
    }
}
