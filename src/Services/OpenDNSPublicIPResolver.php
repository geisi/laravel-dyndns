<?php

namespace Geisi\DynDns\Services;

use Geisi\DynDns\Contracts\DiscoversIpAddress;

/**
 *
 */
class OpenDNSPublicIPResolver implements DiscoversIpAddress
{
    /**
     * Get the servers current public IP address with dig and the opendns my ip resolver
     * @return string
     */
    public function getIp(): string
    {
        return trim((string) shell_exec("dig -4 +short myip.opendns.com @resolver1.opendns.com"));
    }
}
