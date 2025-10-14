<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

use App\Modules\Setting\Repository\SettingRepository;

class Settings
{
    public Common $common;
    public Coupon $coupon;
    public Parser $parser;
    public Web $web;
    public Mail $mail;
    public Notification $notification;
    public Image $image;

    public function __construct()
    {

        $repository = new SettingRepository();
        $this->common = $repository->getCommon();
        $this->coupon = $repository->getCoupon();
        $this->parser = $repository->getParser();
        $this->web = $repository->getWeb();
        $this->mail = $repository->getMail();
        $this->notification = $repository->getNotification();
        $this->image = $repository->getImage();

    }
}
