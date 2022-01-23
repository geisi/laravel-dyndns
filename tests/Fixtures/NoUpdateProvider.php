<?php

namespace Geisi\DynDns\Tests\Fixtures;

use Geisi\DynDns\DynDnsProvider;

class NoUpdateProvider extends DynDnsProvider
{
    protected function getRecordIp(): string
    {
        return "8.8.8.8";
    }

    protected function updateRecord(string $newIp): bool
    {
        return true;
    }
}
