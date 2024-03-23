<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 9/17/18
 * Time: 3:01 PM
 * @author Michael Munger <mj@hph.io>
 */
namespace hphio\cli;

include("TestTreeStructureTrait.php");

use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    use TreeTestingTraits;

    /**
     * @covers \hphio\cli\Tree::setTree()
     * @dataProvider providerTestSetTree
     */
    public function testSetTree($readline_input, $expected) {

        $Tree = new Tree();
        $Tree->setTree($this->getSampleTreeStructure());

        $this->assertCount(4,$Tree->tree);
        $this->assertCount(3,$Tree->tree['user']);
        $this->assertCount(2,$Tree->tree['user']['password']);
        $this->assertCount(2,$Tree->tree['show']);

    }

    public function providerTestSetTree() {

        return  [ [''     , ['user', 'show', 'foo']                ]
                , ['user' , ['password', 'deactivate', 'activate'] ]
                , ['user ', ['password', 'deactivate', 'activate'] ]
                ];
    }

    /**
     * @covers \hphio\cli\Tree::processSingleToken
     * @dataProvider providerTestParseSingleToken
     */
    public function testParseSingleToken($token, $expected) {
        $Tree = new Tree();
        $Tree->setTree($this->getSampleTreeStructure());
        $results = $Tree->processSingleToken($token);
        $this->assertSame($expected,$results);

    }

    public function providerTestParseSingleToken() {
        return  [ [''        , ['user', 'show', 'foo', 'baz']         ]
                , ['user'    , ['password', 'deactivate', 'activate'] ]
                , ['user '   , ['password', 'deactivate', 'activate'] ]
                , ['show'    , ['help', 'users'                     ] ]
                , ['show '   , ['help', 'users'                     ] ]
                , ['sh'      , ['show'                              ] ]
                , ['sh '     , ['show'                              ] ]
                , ['foo'     , ['bar']                                ]
                ];
    }

    /**
     * @param $partial
     * @param $expected
     * @dataProvider providerTestGetTokensThatStartWith
     */
    public function testGetTokensThatStartWith($partial, $expected) {
        $Tree = new Tree();
        $Tree->setTree($this->getSampleTreeStructure());
        $results = $Tree->getTokensThatStartWith($partial, $this->getSampleTreeStructure());
        $this->assertSame($expected,$results);
    }

    public function providerTestGetTokensThatStartWith() {
        return  [ [ 'u'     , ['user']          ]
                , [ 'us'    , ['user']          ]
                , [ 'use'   , ['user']          ]
                , [ 'user'  , ['user']          ]
                , [ 'u '    , false             ]
                , [ 'us '   , false             ]
                , [ 'use '  , false             ]
                , [ 'user ' , false             ]
                ];
    }

    /**
     * @param $tokens
     * @param $expected
     * @dataProvider providerTestProcessMultipleTokens
     */
    public function testProcessMultipleTokens($tokens, $expected) {
        $Tree = new Tree();
        $Tree->setTree($this->getSampleTreeStructure());
        $results = $Tree->processMultipleTokens($tokens);
        $this->assertSame($expected,$results);
    }

    public function providerTestProcessMultipleTokens() {
        return  [
                    [ 'user password' ,  ['reset', 'invalidate'] ]
                ,   [ 'baz bar'       ,  ['foo']                 ]
                ];
    }

}