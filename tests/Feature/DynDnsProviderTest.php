<?php

use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Geisi\DynDns\Tests\Fixtures\DummyIpAddressResolver;
use Geisi\DynDns\Tests\Fixtures\FailureProvider;
use Geisi\DynDns\Tests\Fixtures\HasToBeUpdatedProvider;
use Geisi\DynDns\Tests\Fixtures\NoUpdateProvider;
use Illuminate\Support\Facades\Event;

it('handles dyndns updates', function () {
    $event = Event::fake();
    $provider = new HasToBeUpdatedProvider("foo.bar.com", new DummyIpAddressResolver(), []);
    $provider->handle();

    $event->assertDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(DynDNSUpdateError::class);
});

it('does nothing when ip address has not changed', function () {
    $event = Event::fake();
    $provider = new NoUpdateProvider("foo.bar.com", new DummyIpAddressResolver(), []);
    $provider->handle();

    $event->assertNotDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(DynDNSUpdateError::class);
});

it('handles dyndns errors', function () {
    $event = Event::fake();
    $provider = new FailureProvider("foo.bar.com", new DummyIpAddressResolver(), ['domain_name' => 'foo.bar.com']);
    $provider->handle();

    $event->assertNotDispatched(DynDNSUpdated::class);
    $event->assertDispatched(DynDNSUpdateError::class, function ($args) {
        expect($args->domain) === 'foo.bar.com';
        return true;
    });
});
