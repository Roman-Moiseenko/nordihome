<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Parser extends AbstractSetting
{
    public float $parser_coefficient = 0;
    public int $parser_delivery = 0;
    public int $cost_weight = 0;
    public int $cost_weight_fragile = 0;
    public int $cost_sanctioned = 0;
    public int $cost_retail= 0;

    public int $parser_delivery_0 = 0;
    public int $parser_delivery_1 = 0;
    public int $parser_delivery_2 = 0;
    public int $parser_delivery_3 = 0;
    public int $parser_delivery_4 = 0;
    public int $parser_delivery_5 = 0;
    public int $parser_delivery_6 = 0;
    public int $parser_delivery_7 = 0;
    public int $parser_delivery_8 = 0;
    public int $parser_delivery_9 = 0;
    public int $parser_delivery_10 = 0;

    public bool $with_proxy = false;
    public string $proxy_ip = '';
    public string $proxy_user = '';


    public function view()
    {
        return view('admin.settings.parser', ['parser' => $this]);
    }
}
