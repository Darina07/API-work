<?php

namespace Supp\Api\Tickets;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesTicketsReport implements TicketsReportInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @throws Exception
     */
    public function getTicketsReport(ServerRequestInterface $request, array $routeArgs = []): array
    {
        throw new Exception("Permission denied", 403);
    }

}