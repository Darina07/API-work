<?php

namespace Supp\Api\Projects;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class AdminNewProject implements NewProjectInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function saveNewProject(ServerRequestInterface $request, array $routeArgs = [])
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);
        $project = $this->createProject($requestObj);
        $data = $this->loadProject($project);

        return $data;
    }

    private function createProject($requestObj)
    {
        $sql = <<<SQL
INSERT INTO projects (name, company)
VALUES (:name, :company);
SQL;

        $values = [
            'name' => $requestObj['project_name'],
            'company' => $requestObj['client_id']
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $runner->run();
        return $runner->lastInsertId();
    }

    private function loadProject($project)
    {
        $sql = <<<SQL
SELECT * FROM projects
WHERE id = :project_id;
SQL;

        $values = [
            'project_id' => $project
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}