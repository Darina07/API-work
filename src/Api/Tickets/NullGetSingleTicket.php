<?php

namespace Supp\Api\Tickets;

use League\Container\Container;

abstract class NullGetSingleTicket
{
    protected ?Container $container = null;
    public function __construct(Container $container) {
        $this->container = $container;
    }
}