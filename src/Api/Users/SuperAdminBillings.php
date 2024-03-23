<?php

namespace Supp\Api\Users;

use DateInterval;
use DateTime;
use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Time\TimeConverter;

class SuperAdminBillings implements UserBillingsInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getBillings(ServerRequestInterface $request, array $routeArgs = []): array
    {
        $data = [];
        $data['total_monthly'] = $this->getTotalMonthlyData();
        $data['chart'] = $this->getChartData();
        $data['project_billings'] = $this->getProjectBillings();
        $data['user_billings'] = $this->getUserBillingsData($request);
       // $data['total_per_user_project'] = $this->getTotalBillingPerUserPerProject();

        return $data;
    }

    private function getTotalMonthlyData()
    {
        $sql = <<<SQL
SELECT SUM(b.billed) as total_monthly_seconds
FROM billings b;
SQL;

        $values = [];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) return 0;

        return $this->secondsToMinutesSeconds($result['total_monthly_seconds']);
    }

    private function getChartData()
    {
      $today = clone $this->container->get(DateTime::class);
      $res['dates'] = $this->getMonthArray(clone $today);
      $res['data'] = $this->getData($res['dates']);
      return $res;
    }

  public function getMonthArray(DateTime $anchorDate)
  {
    $buffer = [];
    $startDate = $anchorDate->sub(new DateInterval('P30D'));
    $datePointer = clone $startDate;
    $oneDay = new DateInterval('P1D');

    while (count($buffer) <= 30) {
        array_push($buffer, $datePointer->format('M-d'));
      $datePointer->add($oneDay);
    }
    return array_values($buffer);
  }

  private function getData ($montlyArr)
  {
    $sql = <<<SQL
SELECT date_format(completed_date, '%b-%d') as completed_date, SUM(round(billed / 60) ) AS total_billed_minutes
FROM billings
WHERE completed_date >= DATE_SUB(:today, INTERVAL 31 DAY)
GROUP BY DATE(completed_date)
ORDER BY DATE(completed_date) DESC;
SQL;

    $today = clone $this->container->get(DateTime::class);
    $values = ['today' =>  (clone $today)->format('Y-m-d')];

    $runner = $this->container->get(QueryRunner::class);
    $runner->useQuery($sql);
    $runner->withValues($values);
    $stmt = $runner->run();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $res = [];

    foreach ($result as $v){
        $res[$v['completed_date']] = $v['total_billed_minutes'];
    }

      foreach ($montlyArr as $date) {
          if (!array_key_exists($date, $res)) {
              $res[$date] = 0;
          }
      }
      ksort($res);
    return array_values($res);

  }

    private function getProjectBillings()
    {
        $sql = <<<SQL
SELECT
    b.project AS project_id,
    p.name AS project,
    SUM(ROUND(b.billed / 60)) AS total_billed
FROM billings b
JOIN projects p ON b.project = p.id
WHERE b.completed_date >= DATE_SUB(:today, INTERVAL 31 DAY)
GROUP BY b.project, p.name;
SQL;
        $today = clone $this->container->get(DateTime::class);
        $values = ['today' =>  (clone $today)->format('Y-m-d')];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $res = [];
        foreach ($result as $k=>$v){
            $res[$k]['project_id'] = $v['project_id'];
            $res[$k]['project'] = $v['project'];
            $res[$k]['total_billed'] = $this->secondsToMinutesSeconds($v['total_billed']);
        }

        return $res;
    }

  private function getUserBillingsData($request)
    {
        $sql = <<<SQL
SELECT
    distinct user,
    CONCAT(u.first," ",u.last) as user_name
FROM billings
JOIN users u on u.id = billings.user
ORDER BY user;
SQL;
        $values = [];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $res = [];
        foreach ($result as $k=>$v){
            $res[$k]['user'] = $v['user'];
            $res[$k]['user_name'] = $v['user_name'];
            $res[$k]['total_billed'] = $this->decorateUserTotalBilled($v['user']);
            $res[$k]['billings'] = $this->decorateUserWithBillings($v['user'], $request);
        }
        return $res;
    }

    public function secondsToMinutesSeconds($seconds) {
      $minutes = floor($seconds / 60);
      $remainingSeconds = $seconds % 60;
      return sprintf("%d:%02d", $minutes, $remainingSeconds);
    }

    private function decorateUserTotalBilled($user_id)
    {
        $sql = <<<SQL
SELECT SUM(b.billed) as total_monthly_seconds
FROM billings b
WHERE user = :user_id;
SQL;

        $values = [
            "user_id" => $user_id
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) return 0;

        return $this->secondsToMinutesSeconds($result['total_monthly_seconds']);
    }

    private function decorateUserWithBillings($user_id, $request)
    {
        $sql = <<<SQL
SELECT b.id,
       b.project as project_id,
       p.name as project,
       b.billed, b.created_on,
       DATE_FORMAT(b.created_on, '%a, %e %b %h:%i %p') as created_on_formatted,
       b.completed_date,
       DATE_FORMAT(b.completed_date, '%a, %e %b %h:%i %p') as completed_date_formatted,
       b.completed,description,
       CONCAT(u.first, ' ', u.last) as user_name
FROM billings b
JOIN projects p on p.id = b.project
JOIN users u on b.user = u.id
WHERE b.user=:user_id;
SQL;
        $values = ['user_id' => $user_id];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $k => $v) {
            $userIP = isset($request->getServerParams()['REMOTE_ADDR']) ? $request->getServerParams()['REMOTE_ADDR'] : $request->getServerParams()['SERVER_ADDR'];
            $timeConverter = new TimeConverter($this->container, $userIP);

            $result[$k]['created_on_user_timezone'] = ($v['created_on']) ? $timeConverter->convertToUserTimezone($v['created_on']) : null;
            $result[$k]['completed_date_user_timezone'] = ($v['completed_date']) ? $timeConverter->convertToUserTimezone($v['completed_date']) : null;
        }

        return $result;
    }

//    private function getTotalBillingPerUserPerProject()
//    {
//        $sql = <<<SQL
//SELECT
//  p.name as project_name,
//  CONCAT(u.first, ' ', u.last) as user_name,
//  SUM(b.billed) as total_billed,
//  CONCAT(
//    FLOOR(SUM(b.billed) / 3600), 'h ',
//    FLOOR((SUM(b.billed) % 3600) / 60), 'm ',
//    SUM(b.billed) % 60, 's'
//  ) as total_billed_formatted
//FROM billings b
//JOIN projects p on p.id = b.project
//JOIN users u on b.user = u.id
//WHERE completed = 1
//GROUP BY user_name;
//SQL;
//        $values = [];
//
//        $runner = $this->container->get(QueryRunner::class);
//        $runner->useQuery($sql);
//        $runner->withValues($values);
//        $stmt = $runner->run();
//        return $stmt->fetchAll(PDO::FETCH_ASSOC);
//    }

}