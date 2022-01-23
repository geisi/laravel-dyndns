<?php

namespace Geisi\DynDns\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DynDNSUpdateError
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public string $domain, public string $error)
    {
    }
}
