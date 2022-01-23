<?php

namespace Geisi\DynDns\Services;

use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\Zones;
use Geisi\DynDns\Contracts\DiscoversIpAddress;
use Geisi\DynDns\DynDnsProvider;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 *
 */
class CloudflareDynDNS extends DynDnsProvider
{
    /**
     * @param  string  $domain
     * @param  DiscoversIpAddress  $ipAddressResolver
     * @param  array  $configuration
     */
    public function __construct(
        public string $domain,
        public DiscoversIpAddress $ipAddressResolver,
        public array $configuration
    ) {
        parent::__construct($this->domain, $this->ipAddressResolver, $this->configuration);
    }

    /**
     * Updates the DNS record with the new IP address
     * @param  string  $newIp
     * @return bool
     * @throws BindingResolutionException
     */
    protected function updateRecord(string $newIp): bool
    {
        $result = app()->make(DNS::class)->updateRecordDetails(
            $this->configuration['zone_id'],
            $this->configuration['record_id'],
            array_merge(
                $this->configuration['dns_service']['record_details'] ?? [],
                [
                    'name' => $this->domain,
                    'content' => $newIp,
                ]
            )
        );

        if ($result->success === true) {
            return true;
        }

        $this->errors = (string) $result;

        return false;
    }

    /**
     * Gets the records IP address
     * @return string
     */
    protected function getRecordIp(): string
    {
        if (empty($this->configuration['zone_id'])) {
            $this->configuration['zone_id'] = $this->getZoneID();
        }

        if (empty($this->configuration['record_id'])) {
            $this->configuration['record_id'] = $this->getRecordID();
        }

        return $this->getRecordContent();
    }

    /**
     * get the domains ZoneID
     * @return string
     * @throws EndpointException
     * @throws BindingResolutionException
     */
    protected function getZoneID(): string
    {
        return app()->make(Zones::class)->getZoneID($this->getMainDomain());
    }

    /**
     * Get the record ID from the Zone
     * @return string
     * @throws BindingResolutionException
     */
    protected function getRecordID(): string
    {
        return app()->make(DNS::class)->getRecordID($this->configuration['zone_id'], "A", $this->domain);
    }

    /**
     * Get the record content
     * @return string
     * @throws BindingResolutionException
     */
    protected function getRecordContent(): string
    {
        return (app()->make(DNS::class)->getRecordDetails(
            $this->configuration['zone_id'],
            $this->configuration['record_id']
        ))->content;
    }
}
