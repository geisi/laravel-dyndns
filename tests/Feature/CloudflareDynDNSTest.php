<?php

use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Geisi\DynDns\Services\CloudflareDynDNS;
use Geisi\DynDns\Tests\Fixtures\DummyIpAddressResolver;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;

it('handles dyndns updates', function () {
    $event = Event::fake();

    $this->mock(Zones::class, function (MockInterface $mock) {
        $mock->shouldReceive('getZoneId')
            ->once()
            ->andReturn('fooZoneId');
    });

    $this->mock(DNS::class, function (MockInterface $mock) {
        $mock->shouldReceive('getRecordId')
            ->once()
            ->andReturn('fooRecordId')
            ->shouldReceive('getRecordDetails')
            ->once()
            ->andReturn((object) ['content' => '1.1.1.1'])
            ->shouldReceive('updateRecordDetails')
            ->with('fooZoneId', 'fooRecordId', [
                'name' => 'foo.bar.com',
                'content' => '8.8.8.8',
            ])
            ->once()
            ->andReturn((object) ['success' => true]);
    });

    $provider = new CloudflareDynDNS("foo.bar.com", new DummyIpAddressResolver(), ['api_token' => 'foobar']);
    $provider->handle();

    $event->assertDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(DynDNSUpdateError::class);
});

it('does not update dns when ip has not changed', function () {
    $event = Event::fake();
    $this->mock(Zones::class, function (MockInterface $mock) {
        $mock->shouldReceive('getZoneId')
            ->once()
            ->andReturn('fooZoneId');
    });

    $this->mock(DNS::class, function (MockInterface $mock) {
        $mock->shouldReceive('getRecordId')
            ->once()
            ->andReturn('fooRecordId')
            ->shouldReceive('getRecordDetails')
            ->once()
            ->andReturn((object) ['content' => '8.8.8.8']);
    });

    $provider = new CloudflareDynDNS("foo.bar.com", new DummyIpAddressResolver(), ['api_token' => 'foobar']);


    $provider->handle();

    $event->assertNotDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(DynDNSUpdateError::class);
});

it('dispatches a DynDNSUpdateError event when it fails', function () {
    $event = Event::fake();

    $this->mock(Zones::class, function (MockInterface $mock) {
        $mock->shouldReceive('getZoneId')
            ->once()
            ->andReturn('fooZoneId');
    });

    $this->mock(DNS::class, function (MockInterface $mock) {
        $mock->shouldReceive('getRecordId')
            ->once()
            ->andThrow(new Exception('oh damn...'));
    });

    $provider = new CloudflareDynDNS("foo.bar.com", new DummyIpAddressResolver(), ['api_token' => 'foobar']);
    $provider->handle();

    $event->assertDispatched(DynDNSUpdateError::class, function ($args) {
        expect($args->error)->toBe('oh damn...');

        return true;
    });

    $event->assertNotDispatched(DynDNSUpdated::class);
});
