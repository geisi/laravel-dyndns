<?php

namespace Geisi\DynDns\Commands;

use Geisi\DynDns\Facades\DynDns;
use Illuminate\Console\Command;

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
