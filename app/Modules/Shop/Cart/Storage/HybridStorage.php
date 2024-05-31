<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Shop\Cart\CartItem;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;

class HybridStorage implements StorageInterface
{
    private StorageInterface $storage;

    /**
     * @throws BindingResolutionException
     */
    public function load(): array
    {
        return $this->getStorage()->load();
    }

    /**
     * @throws BindingResolutionException
     */
    private function getStorage()
    {
        $user_id = Auth::guard('user')->check() ? Auth::guard('user')->user()->id : null;
        if (empty($this->storage)) {
            $cookieStorage = app()->make(CookieDBStorage::class);
            if (is_null($user_id)) {
                $this->storage = $cookieStorage;
                //throw new \DomainException('user_id - null' . Auth::guard('user')->user()->id . Auth::guard('user')->check());
            } else {
                $dbStorage = app()->make(DBStorage::class);
                if ($cookieItems = $cookieStorage->load()) {
                    $dbItems = $dbStorage->load();


                    foreach ($cookieItems as $cookieItem) {
                        $_notDB = true;
                        foreach ($dbItems as $dbItem) {
                            if ($dbItem->isProduct($cookieItem->getProduct()->id)) {
                                $dbStorage->plus($dbItem, $cookieItem->quantity);
                                $_notDB = false;
                            }
                        }
                        if ($_notDB) $dbStorage->add($cookieItem);
                    }
                    $cookieStorage->clear();
                }
                $this->storage = $dbStorage;
            }
        }
        return $this->storage;
    }

    public function add(CartItem $item): void
    {
        $this->getStorage()->add($item);
    }

    public function sub(CartItem $item, int $quantity): void
    {
        $this->getStorage()->sub($item, $quantity);
    }

    public function plus(CartItem $item, int $quantity): void
    {
        $this->getStorage()->plus($item, $quantity);
    }


    public function remove(CartItem $item): void
    {
        $this->getStorage()->remove($item);
    }

    public function clear(): void
    {
        $this->getStorage()->clear();
    }

    public function check(CartItem $item): void
    {
        $this->getStorage()->check($item);

    }

}
