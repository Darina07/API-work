<?php
/**
 * Iterable collection of commands for the HPHIO CLI.
 *
 * Date: 9/17/18
 * Time: 6:25 PM
 * @author Michael Munger <mj@hph.io>
 * @license MIT
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;


use \Exception;
use \Iterator;
use \Countable;

class AvailableCommands implements Iterator, Countable
{
    private $position     = 0;
    private $invokerArray = [];

    public function __construct() {
        $this->rewind();
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->invokerArray[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        $this->position++;
    }

    public function previous() {
        $this->position--;
    }

    public function valid() {
        return isset($this->invokerArray[$this->position]);
    }

    public function add(AbstractCommand $Invoker) {
        $this->invokerArray[] = $Invoker;
    }

    public function count() {
        return count($this->invokerArray);
    }

}
