<?php

namespace Geisi\DynDns;

use Exception;
use Geisi\DynDns\Contracts\DiscoversIpAddress;
use Geisi\DynDns\Events\DynDNSUpdated;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Geisi\DynDns\Notifications\PublicIPChangedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

abstract class DynDnsProvider
{
    protected string $errors = '';

    public function __construct(
        public string $domain,
        public DiscoversIpAddress $ipAddressResolver,
        public array $configuration
    ) {
    }

    /**
     * Handles the synchronization with the Dns Service
     * @return void
     * @throws Exception
     */
    final public function handle(): void
    {
        try {
            $recordIp = $this->getRecordIp();
            $publicIP = $this->ipAddressResolver->getIp();

            if ($recordIp != $publicIP) {
                if ($this->updateRecord($publicIP)) {
                    event($event = new DynDNSUpdated(domain: $this->domain, newIp: $publicIP, oldIp: $recordIp));
                    if (isset($this->configuration['notification_email']) && !empty($notificationEmail = $this->configuration['notification_email'])) {
                        Notification::route('mail', $notificationEmail)
                            ->notify(new PublicIPChangedNotification($event));
                    }
                } else {
                    event(new DynDNSUpdateError($this->domain, $this->errors));
                }
            }
        } catch (Exception $exception) {
            Log::error($this->domain.' DynDNS sync error:'.$exception->getMessage(),
                [$exception]);
            event(new DynDNSUpdateError($this->domain, $exception->getMessage()));
        }
    }

    /*
     * Returns the IP address which is currently used
     */
    protected function getRecordIp(): string
    {
        throw new Exception('getRecordIp method has to be implemented!');
    }

    /*
     * Updates the IP storage with the new value
     * @param string $newIp
     * @return bool
     */
    protected function updateRecord(string $newIp): bool
    {
        throw new Exception('updateRecord method has to be implemented!');
    }

    /**
     * returns the main domain from the domain attribute
     * example: domain: test.foobar.com returns: foobar.com
     * @param  string|null  $domain
     * @return string The main domain
     */
    protected function getMainDomain(?string $domain = null): string
    {
        $domain = $domain ?? strtolower(trim($this->domain));
        $count = substr_count($domain, '.');
        if ($count === 2) {
            if (strlen(explode('.', $domain)[1]) > 3) {
                $domain = explode('.', $domain, 2)[1];
            }
        } elseif ($count > 2) {
            $domain = $this->getMainDomain(explode('.', $domain, 2)[1]);
        }

        return $domain;
    }
}
