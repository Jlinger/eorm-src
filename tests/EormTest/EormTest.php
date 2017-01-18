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

    }

    public function testCount()
    {
        $this->assertEquals(
            2,
            Example::count()
        );
    }

    public function testQuery()
    {
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            Example::query()->get()->result()->toArray()
        );
    }

    public function testWhere()
    {
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ],
            Example::where('id', 1)->get()->result()->toArray()
        );

        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            Example::where('name', ['Test2'])->get()->result()->toArray()
        );
    }

    public function testStorage()
    {
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'USA'],
                ['id' => '4', 'name' => 'Test4', 'age' => '30', 'country' => 'USA'],
            ],
            Example::all()->insert([
                'name'    => ['Test3', 'Test4'],
                'age'     => [20, 30],
                'country' => 'USA',
            ])->result()->toArray()
        );

        Example::find([1, 3])->delete();

        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '4', 'name' => 'Test4', 'age' => '30', 'country' => 'USA'],
            ],
            Example::all()->result()->toArray()
        );

        $this->assertEquals(
            [
                ['id' => '5', 'name' => 'Test2', 'age' => '40', 'country' => 'China'],
                ['id' => '6', 'name' => 'Test4', 'age' => '40', 'country' => 'USA'],
            ],
            Example::all()->set('age', 40)->replace()->result()->toArray()
        );
    }
}
