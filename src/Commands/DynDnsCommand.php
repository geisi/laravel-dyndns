<?php

namespace Geisi\DynDns\Commands;

use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Geisi\DynDns\Facades\DynDns;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class DynDnsCommand extends Command
{
    public $signature = 'dyndns:run';

    public $description = 'Run dyndns entries sync by public IP';

    public function handle(): int
    {
        DynDns::handle();

        $this->comment('All domains were synced!');

        return self::SUCCESS;
    }
}
