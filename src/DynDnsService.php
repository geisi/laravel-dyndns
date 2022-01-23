<?php

namespace Geisi\DynDns;

use Geisi\DynDns\Exceptions\WrongDynDnsConfigurationException;

class DynDnsService
{
    public function handle(): void
    {
        foreach (config('dyndns.domains') as $domainConfiguration) {
            app()->make($domainConfiguration['dns_service']['adapter'], [
                'domain' => $domainConfiguration['domain_name'] ?? throw new WrongDynDnsConfigurationException('No Domain name is configured!'),
                'ipAddressResolver' => app()->make($domainConfiguration['resolver_service'] ?? throw new WrongDynDnsConfigurationException('No Resolver Service for domain: '.$domainConfiguration['domain_name'].' defined')),
                'configuration' => $domainConfiguration
            ])->handle();
        }
    }
}
