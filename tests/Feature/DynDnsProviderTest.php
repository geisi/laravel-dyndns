<?php

use Geisi\DynDns\DynDnsProvider;
use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\UpdateDnyDNSIPError;
use Geisi\DynDns\Tests\Fixtures\DummyIpAddressResolver;

it('handles dyndns updates', function () {
    $event = \Illuminate\Support\Facades\Event::fake();
    $provider = new HasToBeUpdatedProvider("foo.bar.com", new DummyIpAddressResolver(), []);
    $provider->handle();

    $event->assertDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(UpdateDnyDNSIPError::class);
});

it('does nothing when ip address has not changed', function () {
    $event = \Illuminate\Support\Facades\Event::fake();
    $provider = new NoUpdateProvider("foo.bar.com", new DummyIpAddressResolver(), []);
    $provider->handle();

    $event->assertNotDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(UpdateDnyDNSIPError::class);
});

it('handles dyndns errors', function () {
    $event = \Illuminate\Support\Facades\Event::fake();
    $provider = new FailureProvider("foo.bar.com", new DummyIpAddressResolver(), []);
    $provider->handle();

    $event->assertNotDispatched(DynDNSUpdated::class);
    $event->assertDispatched(UpdateDnyDNSIPError::class);
});


class HasToBeUpdatedProvider extends DynDnsProvider
{
    protected function getRecordIp(): string
    {
        return "1.1.1.1";
    }

    protected function updateRecord(string $newIp): bool
    {
        return true;
    }
}

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
