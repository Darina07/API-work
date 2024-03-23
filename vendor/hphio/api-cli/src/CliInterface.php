<?php
/**
 * CLI Interface
 *
 * Date: 9/18/18
 * Time: 1:17 AM
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;
use \League\Container\Container;

interface CliInterface
{
    function loadCommands();
    function showBanner();
    function getPrompt();
}