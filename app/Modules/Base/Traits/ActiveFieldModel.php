<?php
declare(strict_types=1);

namespace App\Modules\Base\Traits;

use JetBrains\PhpStorm\Pure;

/**
 * @property bool $active
 */
trait ActiveFieldModel
{
    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function activated(): void
    {
        $this->active = true;
        $this->save();
    }

    public function draft(): void
    {
        $this->active = false;
        $this->save();

    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    #[Pure]
    public function status(): string
    {
        if ($this->isActive()) {
            return 'Активен';
        } else {
            return 'Заблокирован';
        }
    }
    //$table->boolean('active')->default(false);
}
