<?php

namespace Geisi\DynDns\Contracts;

interface DiscoversIpAddress
{
    public function getIp(): string;
}
