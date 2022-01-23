<?php

namespace Geisi\DynDns;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Auth\Auth;
use Cloudflare\API\Endpoints\DNS;
use Geisi\DynDns\Commands\DynDnsCommand;
use Geisi\DynDns\Events\DynDNSUpdateError;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

class DynDnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dyndns')
            ->hasConfigFile('dyndns')
            ->hasCommand(DynDnsCommand::class);
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            Event::listen(DynDNSUpdateError::class, function (DynDNSUpdateError $event) {
                $output = new ConsoleOutput();
                $output->writeln("<error>Error updating domain {$event->domain}</error>");
                $output->writeln($event->error);
            });
        }
    }

    public function boot(): void
    {
        $this->app->bind(Auth::class, function () {
            return new APIToken(config('dyndns.cloudflare_api_token'));
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
        ], 'dyndns-config');
    }
}
