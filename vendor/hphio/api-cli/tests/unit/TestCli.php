<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 9/17/18
 * Time: 3:01 PM
 * @author Michael Munger <mj@hph.io>
 */
namespace hphio\cli;

include("StandardContainerTrait.php");

use PHPUnit\Framework\TestCase;
use League\Container\Container;

//use \Exception;

class mockPDO extends \PDO {
    public function __construct()
    {
        //Do nothing.
    }
}

class CLITest extends TestCase
{
    use StandardTestContainerTrait;

    /**
     * @dataProvider providerTestLoadCommands
     */
    public function testLoadCommands($getCommand1, $getHelp1, $getCommand2, $getHelp2, $grammar) {
        $container = new Container();
        $container->add('db',new mockPDO());

        $commandStub1 = $this->createMock(AbstractCommand::class);
        $commandStub1->method('getCommand')->willReturn($getCommand1);
        $commandStub1->method('getHelp')->willReturn($getHelp1);

        $commandStub2 = $this->createMock(AbstractCommand::class);
        $commandStub2->method('getCommand')->willReturn($getCommand2);
        $commandStub2->method('getHelp')->willReturn($getHelp2);

        $this->assertSame($commandStub1->getCommand(), $getCommand1);
        $this->assertSame($commandStub2->getCommand(), $getCommand2);
        $this->assertSame($commandStub1->getHelp(), $getHelp1);
        $this->assertSame($commandStub2->getHelp(), $getHelp2);

        $container->add(AvailableCommands::class);
        $container->add(SeedUUIDs::class, $commandStub1);
        $container->add(ShowHelp::class , $commandStub2);

        $Cli = new StandardCli($container);
        $this->assertCount(1, $Cli->commands);
        //$this->assertSame($grammar, $Cli->grammar);
    }

    public function providerTestLoadCommands() {
                // getCommand1, getHelp1                                                       , getCommand2, getHelp2                                                          , $grammar
        return  [ [ 'XdKViyVZ', "victorfish chloralism incorporealize unargumentatively algate", "OaWdWzwm" , "untinctured paraphasic adulteration elemin unsightliness cesious", ['XdKViyVZ' => null , 'OaWdWzwm' => null] ]
                ];
    }

    /**
     * @param $testArray
     * @param $expectedResult
     * @dataProvider providerTestOnionize
     */
    public function testOnionize($testArray, $expectedResult) {
        $container = $this->getStandardTestContainer();

        $Cli = new StandardCli($container);
        $this->assertSame($expectedResult, $Cli->onionize($testArray));
    }

    public function providerTestOnionize() {
        return  [ [ ['asdf', 'qwer']                                   , ['asdf' => ['qwer' => null]]                                                                   ]
                , [ ["TrFuLnxuovWUCGBKP", "VsbNiAzbUY", "uPCVwkvCppxu"], ["TrFuLnxuovWUCGBKP" => ["VsbNiAzbUY" => ["uPCVwkvCppxu" => null ]]]                           ]
                , [ [ "nudLnhwF", "qZQenjagmuHu", "nSzShOShUPLh", "MLVUSYDeDsqTu"], ["nudLnhwF"  => ["qZQenjagmuHu"  => ["nSzShOShUPLh" => ["MLVUSYDeDsqTu" => null ]]]]]
                , [ ['exit'] , [ 'exit' => null ]                                                                                                                       ]
                ];
    }

}