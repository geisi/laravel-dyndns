<?php

namespace Geisi\DynDns\Services;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use Geisi\DynDns\Contracts\DiscoversIpAddress;
use Geisi\DynDns\DynDnsProvider;

class CloudflareDynDNS extends DynDnsProvider
{
    private Adapter $adapter;

    public function __construct(public string $domain, public DiscoversIpAddress $ipAddress, public array $configuration)
    {
        parent::__construct($this->domain, $this->ipAddress, $this->configuration);
        $this->adapter = new Guzzle(new APIToken($this->configuration['api_token']));
    }

    protected function updateRecord(string $newIp): bool
    {
        $result = $this->getDnsApi()->updateRecordDetails(
            $this->configuration['zone_id'],
            $this->configuration['record_id'],
            array_merge(
                $this->configuration['record_details'],
                ['name' => $this->domain,
                    'content' => $newIp,
                ]
            )
        );

        if ($result->success === true) {
            return true;
        }

        $this->errors = (string)$result;

        return false;
    }

    private function getDnsApi(): DNS
    {
        return new DNS($this->adapter);
    }

    protected function getRecordIp(): string
    {
        if (empty($this->configuration['zone_id'])) {
            $this->configuration['zone_id'] = $this->retrieveZoneId();
        }

        if (empty($this->configuration['record_id'])) {
            $this->configuration['record_id'] = $this->getRecordId();
        }

        return ($this->getDnsApi()->getRecordDetails($this->configuration['zone_id'], $this->configuration['record_id']))->content;
    }

    private function retrieveZoneId(): string
    {
        dd(app()->make(Zones::class, ['adapter' => $this->adapter])->getZoneID($this->getMainDomain()));
        return app()->make(Zones::class, ['adapter' => $this->adapter])->getZoneID($this->getMainDomain());
    }

    /*
     * Updates the IP storage with the new value
     * @param string $newIp
     * @return bool
     */

    private function getRecordId(): string
    {
        return $this->getDnsApi()->getRecordID($this->configuration['zone_id'], "A", $this->domain);
    }
}
