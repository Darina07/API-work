<?php

namespace Supp\Api\Users;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class GetUsersAdmins implements GetUsersInterface
{

    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function getUsers(ServerRequestInterface $request, array $routeArgs = []): array
    {
        $sql = <<<SQL
SELECT
    CONCAT(u.first," ", u.last) AS name,
    r.name AS role,
    u.id
FROM users u
JOIN roles r ON u.role = r.id
SQL;

        $values = [];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}