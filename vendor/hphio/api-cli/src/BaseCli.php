<?php
/**
 * Abstract class that forms the base of all CLI's.
 *
 * Date: 9/17/18
 * Time: 4:35 PM
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2017-2018 High Powered Help, Inc. All rights reserved.
 */

namespace hphio\cli;

use League\CLImate\CLImate;
use League\Container\Container;

include('CliInterface.php');

abstract class BaseCli implements CliInterface
{
    protected ?Container $container     = null;
    protected ?CLImate $climate         = null;
    public ?array $grammar              = [];
    public ?AvailableCommands $commands = null;
    public ?array $directExecute        = [];

    public function __construct($container)
    {
        if($this->isCLI() === false) die("The CLI must only be run via the terminal. No Apache for you!!!\n");

        $this->container = $container;
        $this->climate = $container->get(CLImate::class);
        $this->commands = $container->get(AvailableCommands::class);
        $this->loadCommands();
        $this->loadGrammar();

        $shouldExit = $this->parseArgv();

        //Do not exit during testing.
        if($container->has('testmode')) return;

        if($shouldExit) exit;
    }

    public function parseArgv() {
        if($this->container->has('argv') == false) return false;

        $args = $this->container->get('argv');
        if(count($args) == 1) return false;
        if(strcmp("-x", $args[1]) !== 0) return false;

        $requiresAnotherArg = "-x requires an additional argument, which is the command you want to run. Run 'show help' for more information." . PHP_EOL;
        if(isset($args[2]) === false) {
            echo $requiresAnotherArg;
            return true;
        }

        if(strlen($args[2]) === 0) {
            echo $requiresAnotherArg;
            return true;
        }

        $command = $args[2];
        $commandCount = $this->runCommand($command);

        if($commandCount === 0) {
            echo "Command '$command' could not be found or run. Check your syntax and make sure it really exists. Run 'show help' for more information." . PHP_EOL;
            return true;
        }

        return true;

    }

    public function showBanner()
    {
        $this->climate->out("HPHIO CLI starting up...");
    }

    public function getPrompt(): string {
        return "HPHIO*CLI> ";
    }

    public function showGoodbye() {
        $this->climate->out("CLI shutting down.");
    }

    public function isCLI() {
        return (php_sapi_name() === 'cli');
    }

    public function loadCommands() {

        //Load available commands
        $this->commands->add($this->container->get(ShowHelp::class));

    }

    protected function loadGrammar() {
        $grammar = [];

        foreach($this->commands as $command) {
            $command = $command->getCommand();
            $thisGrammar = $this->onionize(explode(" ", $command));
            $grammar = array_merge_recursive($grammar,$thisGrammar);
        }

        $this->grammar = $grammar;
    }

    public static function onionize($buffer) {

        //pop off the last one to make the seed array.
        $value = null;
        $key = array_pop($buffer);

        $seed = [$key => $value];


        //Wrap the rest outward.

        for($x = count($buffer) -1; $x >= 0; $x--) {
            $key = array_pop($buffer);
            $tmp = [$key => $seed];
            $seed = $tmp;
        }

        return $seed;
    }

    private function runCommand($line) {
        $commandsRun = 0;
        foreach($this->commands as $Command) {
            if($Command->is($line) || $Command->hasAlias($line)){
                $commandsRun++;
                $Command->runCommand($this->container, $this->commands);
            }
        }
        return $commandsRun;
    }
    public function run() {

        $this->showBanner();

        $line = '';

        readline_completion_function([$this,"traverse_tree"]);

        while($line !== "quit" && $line !== 'exit') {
            $line = readline($this->getPrompt());
            $line = strtolower(trim($line));

            $commandsRun = $this->runCommand($line);

            if($commandsRun === 0) $this->climate->out( "Command not recognized.");
        }

        $this->climate->out("CLI shutting down...Good bye!");
        exit;
    }

    function traverse_tree($input, $index, $tree): array
    {
        //Initialize return values
        $return_values = [];
        $tree = $this->grammar;

        //Pull from readline library.
        $rl_info = readline_info();

        //Get everything I typed.
        $readline_input = substr($rl_info['line_buffer'], 0, $rl_info['end']);

        $Tree = new Tree();
        $Tree->setTree($this->grammar);
        return $Tree->getChoices($readline_input);
    }
}
