<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

abstract class BaseGetSingleTicket extends SuppApiService
{
    protected ?Container $container = null;
    public function __construct(Container $container) {
        $this->container = $container;
    }
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        $data = $this->getSingleTicketData($routeArgs['id']);
        $data = $this->getUploadsData($data);
        if(!empty($data['uploads'])){
            $data = $this->setBase64Encode($data);
        }
        $data = $this->decorateWithTicketType($data);
        $data = $this->decorateTicketProgress($data);
        return new JsonResponse($data, 200);
    }

    private function getSingleTicketData($ticket_id)
    {
        $sql = $this->getSQL();
        $values = $this->getValues($ticket_id);
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getSql(): string
    {
        return <<<SQL
SELECT  t.id as id,
        t.title,
        tt.name as type,
        tc.name as category,
        CONCAT(u.first, " ", u.last) as created_by,
        CONCAT(u2.first, " ", u2.last) as assignee,
        ts.name as status,
        t.ticket_type as type_number
FROM tickets t
JOIN ticket_types tt on t.ticket_type = tt.id
JOIN ticket_categories tc on t.category = tc.id
JOIN ticket_status ts on t.status = ts.id
JOIN users u on t.created_by = u.id
LEFT JOIN users u2 on t.assignee = u2.id
WHERE t.id=:ticket_id;
SQL;

    }

    protected function getValues($ticket_id): array
    {
        return ['ticket_id'=>$ticket_id];
    }

    private function decorateWithTicketType($data)
    {
        switch($data['type_number']) {
            case 2: // Feature Request
                return $this->getFeatureRequestData($data);
            case 3: // Change Order
                return $this->getChangeOrder($data);
            case 1: // Bug Report
            default:
                return $this->getBugReportData($data);

        }
    }

    private function getFeatureRequestData($data)
    {
        $sql = <<<SQL
SELECT description FROM feature_requests WHERE ticket =:ticket_id;
SQL;
        $values = ['ticket_id' => $data['id']];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_merge($data, $result);
    }

    private function getBugReportData($data)
    {
        $sql = <<<SQL
SELECT expected,
       actual,
       steps_to_reproduce,
       solution
FROM bug_report
WHERE ticket=:ticket_id;
SQL;
        $values = ['ticket_id' => $data['id']];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_merge($data, $result);
    }

    private function getChangeOrder($data)
    {
        $sql = <<<SQL
SELECT current_feature, required_changes
FROM change_orders WHERE ticket=:ticket_id;
SQL;
        $values = ['ticket_id' => $data['id']];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_merge($data, $result);
    }

    private function decorateTicketProgress(array $data)
    {
        $sql = <<<SQL
select tp.id,
       tp.ticket_id,
       s.name as status,
       tp.user_id,
       concat(u.first, " ", u.last) as user,
       tp.added_on
from ticket_progress tp
join ticket_status s on tp.status = s.id
join users u on tp.user_id = u.id
where ticket_id = :ticket_id
order by tp.added_on asc;
SQL;
        $values = [
            "ticket_id" => $data['id']
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $data['progress'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

  private function setBase64Encode ($data)
  {
      foreach($data['uploads'] as $key => $value)
      {
          $upload = $data['uploads'][$key];

          if(file_exists($upload['storage_path'])) {
            $imagedata = file_get_contents($upload['storage_path']);
            $base64 = base64_encode($imagedata);
            $data['uploads'][$key]['image'] = 'data:image/jpeg;base64,'.$base64;
          }

      }
      return $data;
  }

    private function getUploadsData($data)
    {
        $sql = <<<SQL
SELECT  up.original_file_name,
        up.storage_path,
        up.url
FROM tickets t
JOIN uploads up on t.id = up.ticket
WHERE t.id=:ticket_id;
SQL;

        $values = [
            "ticket_id" => $data['id']
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $data['uploads'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }


}
