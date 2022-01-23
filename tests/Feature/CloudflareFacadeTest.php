<?php

use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Geisi\DynDns\Facades\DynDns;
use Geisi\DynDns\Tests\Fixtures\DummyIpAddressResolver;
use Geisi\DynDns\Tests\Fixtures\FailureProvider;
use Geisi\DynDns\Tests\Fixtures\HasToBeUpdatedProvider;
use Geisi\DynDns\Tests\Fixtures\NoUpdateProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

it('handles domain configurations in batch', function () {
    $event = Event::fake();
    Config::set('dyndns.domains', [
        [
            'domain_name' => 'google.de',
            'dns_service' => [
                'adapter' => HasToBeUpdatedProvider::class,
            ],
            'resolver_service' => DummyIpAddressResolver::class,
            'notification_email' => ''
        ],
        [
            'domain_name' => 'foobar.de',
            'dns_service' => [
                'adapter' => HasToBeUpdatedProvider::class,
            ],
            'resolver_service' => DummyIpAddressResolver::class,
            'notification_email' => ''
        ],
        [
            'domain_name' => 'no-update.com',
            'dns_service' => [
                'adapter' => NoUpdateProvider::class,
            ],
            'resolver_service' => DummyIpAddressResolver::class,
            'notification_email' => ''
        ],
        [
            'domain_name' => 'im-failing.com',
            'dns_service' => [
                'adapter' => FailureProvider::class,
            ],
            'resolver_service' => DummyIpAddressResolver::class,
            'notification_email' => ''
        ]
    ]);
    DynDns::handle();

    $updatedDomains = collect(['google.de', 'foobar.de']);

    $event->assertDispatched(DynDNSUpdated::class, function ($args) use (&$updatedDomains) {
        expect($args->domain)->toBeIn($updatedDomains->toArray());
        $updatedDomains = $updatedDomains->filter(fn($value) => $value !== $args->domain);
        return true;
    });

    $event->assertDispatchedTimes(DynDNSUpdated::class, 2);

    $event->assertDispatched(DynDNSUpdateError::class, function ($args) {
        expect($args->domain)->toBe('im-failing.com');
        return true;
    });
});