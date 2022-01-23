<?php

namespace Geisi\DynDns\Tests\Fixtures;

use Geisi\DynDns\DynDnsProvider;

class FailureProvider extends DynDnsProvider
{
    protected function getRecordIp(): string
    {
        return "1.1.1.1";
    }

    protected function updateRecord(string $newIp): bool
    {
        return false;
    }
}