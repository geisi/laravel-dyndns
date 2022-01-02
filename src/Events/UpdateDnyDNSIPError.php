<?php

namespace Geisi\DynDns\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateDnyDNSIPError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $error)
    {
    }
}
