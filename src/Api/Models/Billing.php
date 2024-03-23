<?php

namespace Supp\Api\Models;

use Exception;
use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDOStatement;

class Billing extends BillingBase
{
    public static function load(Container $container, int $billingId): Billing {
        $sql = "select * from billings b where b.id = :billing_id";
        $values = ['billing_id' => $billingId];
        $runner = $container->get(QueryRunner::class);
        /** @var PDOStatement $stmt */
        $stmt = $runner->useQuery($sql)->withValues($values)->run();
        if($stmt->rowCount() == 0) throw new Exception("Billing $billingId does not exist.", 404);
        return $stmt->fetchObject(Billing::class, [$container]);
    }

    public static function checkForActiveBillingExisting(Container $container, $currentUserId)
    {
        $sql = <<<SQL
SELECT id, created_on, project FROM billings WHERE user=:user_id and completed=0;
SQL;
        $values = [
            'user_id' => $currentUserId
        ];

        $runner = $container->get(QueryRunner::class);
        /** @var PDOStatement $stmt */
        $stmt = $runner->useQuery($sql)->withValues($values)->run();
        if($stmt->rowCount() == 1){
            return $stmt->fetchObject(Billing::class, [$container]);
        }
        return 0;
    }
}