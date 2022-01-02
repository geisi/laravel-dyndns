<?php

namespace Geisi\DynDns\Commands;

use Illuminate\Console\Command;

class DynDnsCommand extends Command
{
    public $signature = 'laravel-dyndns';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
