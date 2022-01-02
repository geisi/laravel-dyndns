<?php

namespace Geisi\DynDns\Commands;

use Illuminate\Console\Command;

class DynDnsCommand extends Command
{
    public $signature = 'dyndns:run';

    public $description = 'Run dyndns entries sync by public IP';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
