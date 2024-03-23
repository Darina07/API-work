<?php

namespace Supp\Api\Projects;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminProjects implements ProjectInterface
{

    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function getProjects(ServerRequestInterface $request, array $routeArgs = [])
    {
        return $this->getProjectsData();
    }

    private function getProjectsData()
    {
        $sql = <<<SQL
select p.id,
       p.name,
       p.company,
       p.created_at,
       c.name as client_name
from projects p
join clients c on p.company = c.id
order by p.name asc;
SQL;

        $values = [];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}