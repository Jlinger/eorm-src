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
    public function testGetTable()
    {
        $this->assertEquals(
            'example',
            Example::getTable()
        );
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals(
            'id',
            Example::getPrimaryKey()
        );
    }

    public function testClean()
    {
        $this->assertTrue(Example::clean());
    }

    public function testInsert()
    {
        $this->assertEquals(
            1,
            Example::insert([
                'name'    => 'Test1',
                'age'     => 10,
                'country' => 'China',
            ])
        );
    }

    public function testFind()
    {
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ],
            Example::find(1)->result()->toArray()
        );
    }

    public function testCreate()
    {
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            Example::create([
                'name'    => 'Test2',
                'age'     => 20,
                'country' => 'China',
            ])->result()->toArray()
        );
    }

    public function testAll()
    {
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            Example::all()->result()->toArray()
        );
    }

    public function testCount()
    {
        $this->assertEquals(
            2,
            Example::count()
        );
    }
}
