<?php

namespace Geisi\DynDns\Services;

use Geisi\DynDns\Contracts\DiscoversIpAddress;

class OpenDNSPublicIPResolver implements DiscoversIpAddress
{
    public function getIp(): string
    {
        return trim(shell_exec("dig -4 +short myip.opendns.com @resolver1.opendns.com"));
    }
}
