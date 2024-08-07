<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Web extends AbstractSetting
{
    public int $paginate = 0;
    public string $logo_img = '';
    public string $logo_alt = '';

}
