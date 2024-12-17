<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Mail extends AbstractSetting
{
    public ?string $mail_domain = '';
    /**
     * В версии до 1 используется только 1 почтовый ящик, далее будет массив
     */
    public ?string $inbox_name ='';
    public ?string $inbox_password ='';
    public bool $inbox_delete = false;



    /**
     * В версии > 1 добавить настройки исходящей почты (с записью в .env)
     */
    public ?string $outbox_name ='';
    public ?string $outbox_password ='';
    public ?string $outbox_from ='';
    public ?string $system_name ='';
    public ?string $system_password ='';
    public ?string $system_from ='';

}
