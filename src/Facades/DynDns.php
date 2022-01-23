<?php

namespace Geisi\DynDns\Facades;

use Geisi\DynDns\DynDnsService;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Geisi\DynDns\DynDnsService
 */
class DynDns extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DynDnsService::class;
    }
}
