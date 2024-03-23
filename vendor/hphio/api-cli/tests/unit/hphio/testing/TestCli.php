<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 10/21/18
 * Time: 1:32 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\testing;


use hphio\cli\BaseCli;
use hphio\cli\NoOp;
use hphio\cli\ShowHelp;

class TestCli extends BaseCli
{

    public function loadCommands() {
        //Load available commands
        $this->commands->add($this->container->get(ShowHelp::class));
        $this->commands->add($this->container->get(NoOp::class));
    }
}