<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class CompanyContact
{
    public string $phone_chief;
    public string $phone_manager;
    public string $email_chief;
    public string $email_manager;
    public string $post_chief; //Должность
    public FullName $chief;
    public string $post_manager; //Должность
    public FullName $manager;

    public function __construct()
    {
        $this->phone_chief  = '';
        $this->phone_manager = '';
        $this->email_chief = '';
        $this->email_manager = '';
        $this->post_chief = '';
        $this->post_manager = '';
        $this->chief = new FullName();
        $this->manager = new FullName();
    }

    public static function create(
        string $phone_chief = '',
        string $phone_manager = '',
        string $email_chief = '',
        string $email_manager = '',
        string $post_chief = '',
        string $post_manager = '',
        FullName $chief = new FullName(),
        FullName $manager = new FullName(),
        array $params = [])
    : self
    {
        $contacts = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (is_string($value) || $value instanceof FullName) {
                    if (isset($contacts->$key))
                        $contacts->$key = $value ?? '';
                }

                if (is_array($value)) {
                    if (isset($contacts->$key))
                        $contacts->$key = new FullName(
                            $value['surname'],
                            $value['firstname'],
                            $value['secondname'],
                        );
                }
            }
        } else {
            $contacts->phone_chief = $phone_chief;
            $contacts->phone_manager = $phone_manager;
            $contacts->email_chief = $email_chief;
            $contacts->email_manager = $email_manager;
            $contacts->post_chief = $post_chief;
            $contacts->post_manager = $post_manager;
            $contacts->chief = $chief;
            $contacts->manager = $manager;
        }
        return $contacts;
    }

    public static function fromArray(?array $params)
    {
        $contacts = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $contacts->$key = new FullName(
                        $value['surname'] ?? '',
                        $value['firstname'] ?? '',
                        $value['secondname'] ?? '',
                    );
                } else {
                    $contacts->$key = $value ?? '';
                }
            }
        }
        return $contacts;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }

}
