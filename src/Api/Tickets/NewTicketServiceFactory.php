<?php

namespace Supp\Api\Tickets;

use League\Container\Container;

class NewTicketServiceFactory
{
    /**
     * @param \League\Container\Container $container
     * @param $filter
     * @return array|mixed|object|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getNewTicket(Container $container, $filter)
    {
        switch ($filter) {
            case 1: //bug report
                return $container->get(BugReport::class);
            case 2: //feature request
                return $container->get(FeatureRequest::class);
            case 3: //change order
                return $container->get(ChangeOrder::class);
            default:
        }
    }
}