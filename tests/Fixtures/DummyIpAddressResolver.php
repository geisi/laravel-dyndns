<?php

namespace Geisi\DynDns\Tests\Fixtures;

use Geisi\DynDns\Contracts\DiscoversIpAddress;

class DummyIpAddressResolver implements DiscoversIpAddress
{
    public function getIp(): string
    {
        return "8.8.8.8";
    }
}
