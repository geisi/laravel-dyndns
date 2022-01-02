<?php

namespace Geisi\DynDns;

use Exception;
use Geisi\DynDns\Contracts\DiscoversIpAddress;
use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\UpdateDnyDNSIPError;
use Geisi\DynDns\Notifications\PublicIPChangedNotification;
use Illuminate\Support\Facades\Notification;

abstract class DynDnsProvider
{
    protected string $errors = '';

    public function __construct(public string $domain, public DiscoversIpAddress $ipAddress, public array $configuration)
    {
    }

    /**
     * Handles the synchronization with the Dns Service
     * @return void
     * @throws Exception
     */
    public final function handle(): void
    {
        $recordIp = $this->getRecordIp();
        $publicIP = $this->ipAddress->getIp();
        if ($recordIp != $publicIP) {
            if ($this->updateRecord($publicIP)) {
                event($event = new DynDNSUpdated(newIp: $publicIP, oldIp: $recordIp));
                if (!empty(config('dyndns.notification_email'))) {
                    Notification::route('mail', 'tim@partysturmevents.de')->notify(new PublicIPChangedNotification($event));
                }
            } else {
                event($event = new UpdateDnyDNSIPError(error: $this->errors));
            }
        }
        dump(true);
    }

    /*
     * Returns the IP address which is currently used
     */
    protected function getRecordIp(): string
    {
        throw new Exception('getRecordIp method has to be implemented!');
        return "";
    }

    /*
     * Updates the IP storage with the new value
     * @param string $newIp
     * @return bool
     */
    protected function updateRecord(string $newIp): bool
    {
        throw new Exception('updateRecord method has to be implemented!');
        return true;
    }

    /**
     * returns the main domain from the domain attribute
     * example: domain: test.foobar.com returns: foobar.com
     * @return string The main domain
     */
    protected function getMainDomain(): string
    {
        $domain = strtolower(trim($this->domain));
        $count = substr_count($domain, '.');
        if ($count === 2) {
            if (strlen(explode('.', $domain)[1]) > 3) $domain = explode('.', $domain, 2)[1];
        } else if ($count > 2) {
            $domain = $this->getMainDomain(explode('.', $domain, 2)[1]);
        }
        return $domain;
    }
}
