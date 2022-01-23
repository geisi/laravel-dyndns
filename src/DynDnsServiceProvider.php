<?php

namespace Geisi\DynDns;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Auth\Auth;
use Cloudflare\API\Endpoints\DNS;
use Geisi\DynDns\Commands\DynDnsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DynDnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dyndns')
            ->hasConfigFile()
            ->hasCommand(DynDnsCommand::class);
    }

    public function boot(): void
    {
        $this->app->bind(Auth::class, function () {
            return new APIToken(config('dyndns.domains.dns_service.api_token'));
        });

        $this->app->bind(Adapter::class, function ($app) {
            return new Guzzle($app->make(Auth::class));
        });

        $this->app->bind(DNS::class, function ($app) {
            return new DNS($app->make(Adapter::class));
        });

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->publishConfigs();
        }
    }

    protected function registerCommands(): void
    {
        $this->commands(DynDnsCommand::class);
    }

    protected function publishConfigs(): void
    {
        $this->publishes([
            __DIR__.'/../config/dyndns.php' => config_path('dyndns.php'),
        ]);
    }
}
