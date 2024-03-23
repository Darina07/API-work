<?php

namespace Supp\Api\Models;

use Hphio\Utils\QueryRunner;
use League\Container\Container;

class BillingBase
{
    /* <database fields> */

    public ?int $id                      = null;
    public ?int $user                    = null;
    public ?int $project                 = null;
    public ?float $billed                = null;
    public ?string $completed_date       = null;
    public ?int $completed               = 0;
    public ?string $created_on           = null;
    public ?string $description          = null;

    /* </database fields> */


    /* <Dependency Injection Fields> */

    public ?Container $container = null;

    /* </Dependency Injection Fields> */

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Returns an associative array of values for this class.
     * @return array
     */

    public function getMyValues() : array {
        $buffer = json_decode(json_encode($this), true);
        unset($buffer['container']);
        return $buffer;
    }

    public function insert() {
        $sql = " INSERT INTO `billings` (
                `user`,
                `project`,
                `billed`,
                `completed_date`,
                `completed`,
                `created_on`,
                `description`
                )
                VALUES
                (
                :user,
                :project,
                :billed,
                :completed_date,
                :completed,
                :created_on,
                :description
                )";
        $values = $this->getMyValues();
        unset($values['id']);

        /** @var QueryRunner $runner */
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();

        $this->id = $runner->lastInsertId();
        return $this->id;

    }

    public function update() {
        $sql = "UPDATE `billings`
                SET
                user = :user,
                project = :project,
                billed = :billed,
                completed_date = :completed_date,
                completed = :completed,
                created_on = :created_on,
                description = :description
                WHERE `id` = :id
                LIMIT 1";

        $values = $this->getMyValues();

        /** @var QueryRunner $runner */
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
    }
}