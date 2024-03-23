<?php

namespace Supp\Api\Projects;

use Exception;
use Hphio\Utils\QueryRunner;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminDeleteProject implements DeleteProjectInterface
{

    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function deleteProject(ServerRequestInterface $request, array $routeArgs = [])
    {
        $projectInBilling = $this->checkProjectInBillings($routeArgs['id']);
        if($projectInBilling) {
            throw new Exception("This project is already in a billing. You can not delete it.", 403);
        }

        $sql = <<<SQL
DELETE FROM projects
WHERE id=:project_id; 
SQL;

        $values = [
            'project_id' => $routeArgs['id']
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values)->run();
    }

    private function checkProjectInBillings($projectID)
    {
        $sql = <<<SQL
SELECT id FROM billings
WHERE project=:project_id;
SQL;

        $values = [
            'project_id' => $projectID
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $stmt = $runner->withValues($values)->run();
        if ($stmt->rowCount() != 0){
            return true;
        }
        return false;
    }
}