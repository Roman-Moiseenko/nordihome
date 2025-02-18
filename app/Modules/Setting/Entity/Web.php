<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Web extends AbstractSetting
{
    public int $paginate = 21;
    public string $logo_img = '';
    public string $logo_alt = '';

    public string $metrika = '';
    public string $google = '';

    public bool $is_cache = false;
    public string $breadcrumbs_home = '<i class="fa-light fa-house"></i>';
    public bool $is_category = true;


    public string $categories_title = '';
    public string $categories_desc = '';
    public string $title_contact = '';
    public string $title_city = '';

}
