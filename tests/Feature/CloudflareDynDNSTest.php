<?php

use Cloudflare\API\Endpoints\Zones;
use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\UpdateDnyDNSIPError;
use Geisi\DynDns\Services\CloudflareDynDNS;
use Geisi\DynDns\Tests\Fixtures\DummyIpAddressResolver;
use Mockery\MockInterface;

it('handles dyndns updates', function () {
    $event = \Illuminate\Support\Facades\Event::fake();

    $mock = $this->mock(Zones::class, function (MockInterface $mock) {
        $mock->shouldReceive('getZoneID')
            ->once()
            ->andReturn('foobar');
    });
//    mock(Zones::class)
//        ->shouldReceive('getZoneID')
//        ->once()
//        ->andReturn('foobar');

    $provider = new CloudflareDynDNS("foo.bar.com", new DummyIpAddressResolver(), ['api_token' => 'foobar']);


    $provider->handle();

    $event->assertDispatched(DynDNSUpdated::class);
    $event->assertNotDispatched(UpdateDnyDNSIPError::class);
});
