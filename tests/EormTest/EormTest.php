<?php
/**
 *+------------------------------------------------------------------------------------------------+
 *| Edoger ORM                                                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| A Simple PHP Object Relational Mapping Library.                                                |
 *+------------------------------------------------------------------------------------------------+
 *| @license   MIT                                                                                 |
 *| @link      https://www.edoger.com/                                                             |
 *| @copyright Copyright (c) 2016 - 2017 Qingshan Luo                                              |
 *+------------------------------------------------------------------------------------------------+
 *| @author    Qingshan Luo <shanshan.lqs@gmail.com>                                               |
 *+------------------------------------------------------------------------------------------------+
 */
namespace EormTest;

use Models\Example;
use PHPUnit\Framework\TestCase;

/**
 * Eorm Library Unit Test Class.
 */
class EormTest extends TestCase
{
    public function testGetTable()
    {
        $this->assertEquals('example', Example::getTable());
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals('id', Example::getPrimaryKey());
    }

    public function testClean()
    {
        $this->assertTrue(Example::clean());
    }

    public function testInsert()
    {
        $actual      = Example::insert(['name' => 'Test1', 'age' => 10, 'country' => 'China']);
        $expectation = 1;

        $this->assertEquals($expectation, $actual);
    }

    public function testFind()
    {
        $actual      = Example::find(1)->result()->toArray();
        $expectation = [['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China']];

        $this->assertEquals($expectation, $actual);
    }

    public function testCreate()
    {
        $actual      = Example::create(['name' => 'Test2', 'age' => 20, 'country' => 'China'])->result()->toArray();
        $expectation = [['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China']];

        $this->assertEquals($expectation, $actual);
    }

    public function testAll()
    {
        $actual      = Example::all()->result()->toArray();
        $expectation = [
            ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
        ];

        $this->assertEquals($expectation, $actual);
    }

    public function testCount()
    {
        $this->assertEquals(2, Example::count());
    }

    public function testQuery()
    {
        $actual      = Example::query()->get()->result()->toArray();
        $expectation = [
            ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
        ];

        $this->assertEquals($expectation, $actual);
    }

    public function testWhere()
    {
        $actual      = Example::where('id', 1)->get()->result()->toArray();
        $expectation = [['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China']];

        $this->assertEquals($expectation, $actual);

        $actual      = Example::where('name', ['Test2'])->get()->result()->toArray();
        $expectation = [['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China']];

        $this->assertEquals($expectation, $actual);
    }

    public function testStorage()
    {
        $actual = Example::all()
            ->insert(['name' => ['Test3', 'Test4'], 'age' => [20, 30], 'country' => 'USA'])
            ->result()
            ->toArray();
        $expectation = [
            ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'USA'],
            ['id' => '4', 'name' => 'Test4', 'age' => '30', 'country' => 'USA'],
        ];

        $this->assertEquals($expectation, $actual);

        // Delete two rows.
        Example::find([1, 3])->delete();

        $actual      = Example::all()->result()->toArray();
        $expectation = [
            ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ['id' => '4', 'name' => 'Test4', 'age' => '30', 'country' => 'USA'],
        ];

        $this->assertEquals($expectation, $actual);

        $actual      = Example::all()->set('age', 40)->replace()->result()->toArray();
        $expectation = [
            ['id' => '5', 'name' => 'Test2', 'age' => '40', 'country' => 'China'],
            ['id' => '6', 'name' => 'Test4', 'age' => '40', 'country' => 'USA'],
        ];

        $this->assertEquals($expectation, $actual);
    }
}
