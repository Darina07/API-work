<?php
/**
 * Displays all commands and their descriptions
 *
 * Date: 9/17/18
 * Time: 4:39 PM
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;
use \League\Container\Container;

class ExitCommand extends AbstractCommand
{
    protected ?string $command = "exit";
    protected ?string $commandHelp = "Exits the CLI";

    public function setAliases() {
        $this->addAlias('quit');
    }

    public function showGoodbye() {
        $this->climate->out("CLI shutting down.");
    }

    public function runCommand(Container $container, AvailableCommands $commands)
    {
        $this->showGoodbye();
        exit;
    }
}
