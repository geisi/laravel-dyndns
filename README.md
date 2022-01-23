# Laravel DynDns easily replace any external DynDNS Service

[![Latest Version on Packagist](https://img.shields.io/packagist/v/geisi/laravel-dyndns.svg?style=flat-square)](https://packagist.org/packages/geisi/laravel-dyndns)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/geisi/laravel-dyndns/run-tests?label=tests)](https://github.com/geisi/laravel-dyndns/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/geisi/laravel-dyndns/Check%20&%20fix%20styling?label=code%20style)](https://github.com/geisi/laravel-dyndns/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/geisi/laravel-dyndns.svg?style=flat-square)](https://packagist.org/packages/geisi/laravel-dyndns)

Laravel DynDNS is a lightweight package to help you to publish your local public IP to DNS records without using any external DynDNS service.
Typically, this is needed when you want to expose any service from your local network to the internet. 
Your ISP provider might change your public IP address after some time. This package keeps your DNS records in sync with your public IP address.

## Installation

You can install the package via composer:

```bash
composer require geisi/laravel-dyndns
```

You have to publish the config file with:

```bash
php artisan vendor:publish --tag="dyndns-config"
```

In the config file you have to set up all of your domains you want to sync with your local public IP address. By default
our Cloudflare DynDNS adapter is used. If you don't already have a Cloudflare Account you can create a Cloudflare
Account at [cloudflare.com](https://www.cloudflare.com/) for free.

When you have finished the domain setup process. You can proceed with configuring the package.

```php
return [
/*
     * List of all domains which should be synchronized with the systems public IP address
     */
    'domains' => [
        [
            /*
             * Your full domain name e.g. dyndns.foo-bar.com
             */
            'domain_name' => env('DYNDNS_DOMAIN_NAME', ''),
            'dns_service' => [
                /*
                 * The dns service which you want to use to serve your Dns records. By default, this package runs with the Cloudflare Dns service.
                 * If you don't want to use Cloudflare you can create your own implementation.
                 * Your class has to implement the Geisi\DynDns\Contracts\HandlesDynDnsRecords interface.
                 */
                'adapter' => \Geisi\DynDns\Services\CloudflareDynDNS::class,

                /*
                 * The Cloudflare zone id this can be left empty. For performance reasons it is recommended to configure a zone id.
                 */
                'zone_id' => env('DYNDNS_ZONE_ID', ''),

                /*
                 * The Cloudflare dns record id this can be left empty. For performance reasons it is recommended to configure a dns record id.
                 */
                'record_id' => env('DYNDNS_RECORD_ID', ''),

                /*
                 * The Cloudflare DNS record details look Cloudflare API documentation for reference.
                 */
                'record_details' => [
                    'type' => 'A',
                    'proxied' => false,
                    'ttl' => 1
                ],

                /*
                 * The Cloudflare API token.
                 */
                'api_token' => env('CLOUDFLARE_TOKEN', '')
            ],
            /*
             * The public IP Address Resolver Service. By default, we are using the opendns.com resolver to query the current public IP address.
             * If you don't want to use opendns.com you can create your own implementation.
             * Your class has to implement the Geisi\DynDns\Contracts\DiscoversIpAddress interface.
             */
            'resolver_service' => Geisi\DynDns\Services\OpenDNSPublicIPResolver::class,
            /*
             * The email address which should be notified when the public IP address changes
             * leave empty if you want to disable mail notifications
             */
            'notification_email' => env('DYNDNS_NOTIFICATION_EMAIL', '')
        ]
    ]
];
```

When you just want a quick setup add following two entries to your .env file:

```
DYNDNS_DOMAIN_NAME="dyndns-subdomain.your-domain.com"
CLOUDFLARE_TOKEN="<enter your cloudflare api token here>"
```

## Usage

You can start the dyndns sync process with following command:
```bash
php artisan dyndns:run
```

We recommend using the laravel scheduler in the App/Console/Kernel.php file to keep your domains in sync with your IP address.
Just add following line to the Kernel.php file schedule method:

````PHP
 $schedule->command('dyndns:run')->everyMinute();
````

When you want to trigger the resync outside of the console you can use the Geisi\DynDns\Facades\DynDns facade.

```PHP
Geisi\DynDns\Facades\DynDns::handle();
```

##Notifications

When you want to be informed when your public IP address has changed you can set the **notification_email** configuration.
Please have in mind that you have to add a working mail configuration for sending out emails.

##Events

Laravel DynDns triggers a Geisi\DynDns\Events\DynDNSUpdated event everytime your IP address changes. 
When the sync process fails a Geisi\DynDns\Events\DynDNSUpdateError event is dispatched.

You can listen for those events and implement your own application logic.

##Adding other DNS Services

Technically every single DNS service which can be configured with a TTL by 1 minute can be used as your DynDNS service.
Today we only support Cloudflare out of the box, but you can easily add another services by extending the **Geisi\DynDns\DynDnsProvider** class. 
You only have to implement two methods **getRecordIp** and **updateRecord**.

After creating a new DynDNSProvider you can add it to your domains configuration.


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [geisi](https://github.com/geisi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
