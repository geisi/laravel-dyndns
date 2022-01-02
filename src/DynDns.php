<?php

namespace Geisi\DynDns;

class DynDns
{
    public static function handle()
    {
        collect(config('dyndns.domains'))->each(function ($domain) {
            $object = new $domain['dns_service']['adapter']($domain['domain_name'], app()->make($domain['discover_service']), $domain['dns_service']);
            $object->handle();
        });
    }
}
