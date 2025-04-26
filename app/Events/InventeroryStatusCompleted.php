<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventeroryStatusCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

}
