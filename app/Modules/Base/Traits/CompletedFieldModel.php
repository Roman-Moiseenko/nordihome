<?php

namespace App\Modules\Base\Traits;

use JetBrains\PhpStorm\Pure;

/**
 * @property bool $completed
 */
trait CompletedFieldModel
{
    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function completed(): void
    {
        $this->completed = true;
        $this->save();
    }

    public function work(): void
    {
        $this->completed = false;
        $this->save();

    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    #[Pure]
    public function status(): string
    {
        if ($this->isCompleted()) {
            return 'Проведен';
        } else {
            return 'В работе';
        }
    }
}
