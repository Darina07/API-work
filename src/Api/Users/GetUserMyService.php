<?php

namespace Supp\Api\Users;

use Exception;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

class GetUserMyService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $data = $this->getUser();
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }

    private function getUser()
    {
        $sql = <<<SQL
SELECT id, username, email, nonce, role, first, last, company_name, created, last_login, activation_status, activated_on,uuid
FROM users
WHERE id = :user_id
SQL;

        $values = ['user_id' => $this->container->get('current_user')->id];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
