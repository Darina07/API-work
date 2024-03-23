<?php
/**
 * Abstract Command, which forms the basis for all CLI comands.
 *
 * Date: 9/17/18
 * Time: 4:33 PM
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;
use League\CLImate\CLImate;
use \League\Container\Container;
use \PDO;

include('CommandInterface.php');

abstract class AbstractCommand
{
    protected ?Container $container = null;
    protected ?CLImate $climate = null;
    protected ?string $command = "foo";
    protected ?string $commandHelp = "bar baz";
    protected ?array $command_aliases = [];
    protected ?PDO $db = null;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->climate = $container->get(CLImate::class);
        $this->db = $container->get('db');
        $this->setAliases();
    }

    public function setAliases() {
        //$this->addAlias('quit');
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getHelp(): ?string
    {
        return $this->commandHelp;
    }

    public function is($command): bool
    {
        return ($command == $this->command);
    }

    public function addAlias($alias) {
        $this->command_aliases[] = $alias;
    }

    public function hasAlias($needle): bool
    {
        return in_array($needle, $this->command_aliases);
    }

    public function getAliases(): ?array
    {
        return $this->command_aliases;
    }


}
