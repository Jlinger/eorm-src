<?php
/**
 *+------------------------------------------------------------------------------------------------+
 *| Edoger ORM                                                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| A Simple PHP Object Relational Mapping Library.                                                |
 *+------------------------------------------------------------------------------------------------+
 *| @license   MIT                                                                                 |
 *| @link      https://www.edoger.com/                                                             |
 *| @copyright Copyright (c) 2016 Qingshan Luo                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| @author    Qingshan Luo <shanshan.lqs@gmail.com>                                               |
 *+------------------------------------------------------------------------------------------------+
 */
namespace EormTest;

use Models\Example;
use PHPUnit\Framework\TestCase;

class EormTest extends TestCase
{
    public function testCommon()
    {
        $this->assertEquals('example', Example::getTable());
        $this->assertEquals('id', Example::getPrimaryKey());
        $this->assertEquals('eorm', Example::getServer());
    }

    public function testCount()
    {
        $this->assertEquals(1, Example::count());
    }

    public function testAll()
    {
        $this->assertEquals(
            [
                [
                    'id'      => '1',
                    'name'    => 'Edoger',
                    'age'     => '25',
                    'country' => 'China',
                ],
            ],
            Example::all()->result()->toArray()
        );
    }
}
