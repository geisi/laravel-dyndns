<?php

use Geisi\DynDns\Facades\DynDns;
use function Pest\Laravel\artisan;

it('calls the handle method', function () {
    DynDns::shouldReceive('handle')->once();

    artisan('dyndns:run')->assertSuccessful();
});
