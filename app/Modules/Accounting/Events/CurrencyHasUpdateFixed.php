<?php

namespace App\Modules\Accounting\Events;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CurrencyHasUpdateFixed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Currency $currency;

    /**
     * Create a new event instance.
     */
    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
