<?php
/**
 * CLI Tree class for navigating tab completion commands.
 *
 * Date: 9/17/18
 * Time: 9:33 PM
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;


class Tree
{
    public $tree = null;

    public function __construct()
    {
        //Can't set the tree here because it's done at run-time.
    }

    public function setTree($tree) {
        $this->tree = $tree;
    }

    public function getTokensThatStartWith($prefix, $tree) {
        $keys = array_keys($tree);
        $buffer = [];

        foreach($keys as $key) {
            if(substr($key,0, strlen($prefix)) !== $prefix) continue;
            $buffer[] = $key;
        }

        return (count($buffer) > 0 ? $buffer : false);

    }

    public function getSubNode($token, $tree) {

        if(is_array($tree[$token]) === false) return [ $tree[$token] ];

        $subNode = $tree[$token];

        $buffer = [];
        return array_keys($subNode);
    }

    public function processSingleToken($token) {

        //Try for a partial match
        $token = trim($token);

        //If the token is empty, return the default keys.
        if(strlen($token) == 0) return array_keys($this->tree);

        //If there is a full match for this single token, return the full match.
        if(array_key_exists($token,$this->tree)) return $this->getSubNode($token,$this->tree);

        //No full match yet. Let's look for partials.
        $buffer = $this->getTokensThatStartWith($token, $this->tree);

        //If there is not even a partial match, return the default keys.
        if($buffer === false) return array_keys($this->tree);

        //If there are multiple matches, return them all.
        if(count($buffer) > 1) return $buffer;

        //Default: return the partial matches
        return $buffer;

    }

    private function padReturn($array) {
        return " $array ";
    }

    public function processMultipleTokens($tokens) {

        $tree = $this->tree;
        $tokens = explode(" ", $tokens);

        foreach($tokens as $token) {

            //break the loop if the key doesn't exist because there is nothing to do.
            if(array_key_exists($token, $tree) === true) {
                //Jump into that sub-tree
                $tree = $tree[$token];
                continue;
            }

            $buffer = $this->getTokensThatStartWith($token,$tree);

            if($buffer === false) break;

            return $buffer;

        }

        /*$return = array_map([$this, 'padReturn'],array_keys($tree));*/
        if(is_null($tree)) return [];
        $return = array_keys($tree);
        return $return;
    }

    public function getChoices($readline_input) {

        $readline_input = trim($readline_input);

        $tokens = explode(" ", $readline_input);

        if(count($tokens) == 1) return $this->processSingleToken($tokens[0]);

        return $this->processMultipleTokens($readline_input, $tokens);
    }


}