<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

use App\Modules\Setting\Repository\SettingRepository;

class Settings
{
    private ?Common $commonCache = null;
    private ?Coupon $couponCache = null;
    private ?Parser $parserCache = null;
    private ?Web $webCache = null;
    private ?Mail $mailCache = null;
    private ?Notification $notificationCache = null;
    private ?Image $imageCache = null;

    private ?SettingRepository $repository = null;

    private function repository(): SettingRepository
    {
        if ($this->repository === null) {
            $this->repository = new SettingRepository();
        }
        return $this->repository;
    }

    public function getCommon(): Common
    {
        if ($this->commonCache === null) {
            $this->commonCache = $this->repository()->getCommon();
        }
        return $this->commonCache;
    }

    public function getCoupon(): Coupon
    {
        if ($this->couponCache === null) {
            $this->couponCache = $this->repository()->getCoupon();
        }
        return $this->couponCache;
    }

    public function getParser(): Parser
    {
        if ($this->parserCache === null) {
            $this->parserCache = $this->repository()->getParser();
        }
        return $this->parserCache;
    }

    public function getWeb(): Web
    {
        if ($this->webCache === null) {
            $this->webCache = $this->repository()->getWeb();
        }
        return $this->webCache;
    }

    public function getMail(): Mail
    {
        if ($this->mailCache === null) {
            $this->mailCache = $this->repository()->getMail();
        }
        return $this->mailCache;
    }

    public function getNotification(): Notification
    {
        if ($this->notificationCache === null) {
            $this->notificationCache = $this->repository()->getNotification();
        }
        return $this->notificationCache;
    }

    public function getImage(): Image
    {
        if ($this->imageCache === null) {
            $this->imageCache = $this->repository()->getImage();
        }
        return $this->imageCache;
    }

    // Магический __get для обратной совместимости
    public function __get(string $name)
    {
        switch ($name) {
            case 'common':
                return $this->getCommon();
            case 'coupon':
                return $this->getCoupon();
            case 'parser':
                return $this->getParser();
            case 'web':
                return $this->getWeb();
            case 'mail':
                return $this->getMail();
            case 'notification':
                return $this->getNotification();
            case 'image':
                return $this->getImage();
            default:
                throw new \RuntimeException("Undefined property: $name");
        }
    }
}
