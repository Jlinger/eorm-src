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

use Eorm\Library\Argument;
use PHPUnit\Framework\TestCase;

class ArgumentTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertEquals([], (new Argument())->toArray());
        $this->assertEquals([1, '2'], (new Argument([1, 'a' => '2']))->toArray());
    }

    public function testCount()
    {
        $this->assertEquals(0, (new Argument())->count());
        $this->assertEquals(2, (new Argument([1, 2]))->count());
    }

    public function testPush()
    {
        $this->assertEquals([1], (new Argument())->push(1)->toArray());
        $this->assertEquals([1, 2, 'a'], (new Argument([1, 2]))->push('a')->toArray());
    }
}
