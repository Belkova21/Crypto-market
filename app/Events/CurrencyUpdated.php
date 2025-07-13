<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CurrencyUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public array $currencies;

    /**
     * Create a new event instance.
     */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('currencies');
    }

    public function broadcastAs(): string
    {
        return 'currency.updated';
    }
}
