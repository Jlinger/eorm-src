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
use Eorm\Library\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testStandardise()
    {
        $this->assertEquals('`name`', Helper::standardise('name'));
        $this->assertEquals(['`name`', '`age`'], Helper::standardise(['name', 'age']));
        $this->assertEquals(['name' => '`age`'], Helper::standardise(['name' => 'age']));
    }

    public function testRange()
    {
        $this->assertEquals([2, 3, 4, 5], Helper::range(2, 4));
        $this->assertEquals(2, Helper::range(2, 1));
    }

    public function testMerge()
    {
        $this->assertEquals([2, 3, 1], Helper::merge([1], [2, 3]));
    }

    public function testFill()
    {
        $this->assertEquals('(?,?)', Helper::fill(2));
        $this->assertEquals('(#,#)', Helper::fill(2, '#'));
        $this->assertEquals('#,#', Helper::fill(2, '#', false));
    }

    public function testToScalar()
    {
        $this->assertEquals(1, Helper::toScalar(1));
        $this->assertEquals('abc', Helper::toScalar('abc'));
        $this->assertEquals(1, Helper::toScalar(true));
        $this->assertEquals(0, Helper::toScalar(false));
        $this->assertEquals('', Helper::toScalar(null));
    }

    public function testMergeField()
    {
        $this->assertEquals('`a`,`b`', Helper::mergeField(['a', 'b']));
    }

    public function testMakeInsertArray()
    {
        $data = Helper::makeInsertArray([
            'a',
            ['b', 'c'],
            [20, 30, 40, 50],
        ]);

        $this->assertTrue($data[0] instanceof Argument);
        $this->assertEquals(12, $data[0]->count());
        $this->assertEquals(
            ['a', 'b', 20, 'a', 'c', 30, 'a', 'c', 40, 'a', 'c', 50],
            $data[0]->toArray()
        );
        $this->assertEquals(4, $data[1]);
        $this->assertEquals(3, $data[2]);
    }

    public function testMakeWhereIn()
    {
        $this->assertEquals('`id`=?', Helper::makeWhereIn('id', 1));
        $this->assertEquals('`id` IN (?,?)', Helper::makeWhereIn('id', 2));
    }
}
