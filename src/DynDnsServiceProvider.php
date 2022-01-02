<?php

namespace Geisi\DynDns;

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
    }
}
