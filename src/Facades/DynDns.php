<?php

namespace Geisi\DynDns\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Geisi\DynDns\DynDns
 */
class DynDns extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-dyndns';
    }
}
