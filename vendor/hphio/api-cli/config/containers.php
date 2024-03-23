<?php

namespace hphio\cli;


use League\Container\Container;

/**
 * Configured the container for the application.
 *
 * Date: 10/8/18
 * Time: 11:29 AM
 * @author Michael Munger <mj@hph.io>
 */

function getContainer() {
    $container = new Container();
    $container->add(AbstractCommand::class);
    $container->add(AvailableCommands::class);
    $container->add(BaseCli::class);
    $container->add(ExitCommand::class);
    $container->add(ShowHelp::class);
    $container->add(StandardCli::class);
    $container-add(Tree::class);
    return $container;
}