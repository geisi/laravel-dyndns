<?php

namespace Geisi\DynDns;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Geisi\DynDns\Commands\DynDnsCommand;

class DynDnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-dyndns')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-dyndns_table')
            ->hasCommand(DynDnsCommand::class);
    }
}
