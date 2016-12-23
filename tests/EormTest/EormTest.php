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

    public function testEorm()
    {
        Example::clean();
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
            ],
            Example::create(
                [
                    'name'    => 'Test1',
                    'age'     => 10,
                    'country' => 'China',
                ]
            )->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
            ],
            Example::create(
                [
                    'name'    => ['Test2', 'Test3'],
                    'age'     => 20,
                    'country' => ['China', 'Germany'],
                ]
            )->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '6', 'name' => 'Test6', 'age' => '20', 'country' => 'Germany'],
            ],
            Example::create(
                [
                    'name'    => ['Test4', 'Test5', 'Test6'],
                    'age'     => [10, 20, 20],
                    'country' => ['China', 'Germany'],
                ]
            )->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::create(
                [
                    'name'    => ['Test7', 'Test8', 'Test9'],
                    'age'     => [40],
                    'country' => ['England', 'China', 'Germany'],
                ]
            )->result()->toArray()
        );
        $this->assertEquals(9, Example::count());
        $this->assertEquals(
            [
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
            ],
            Example::find(5)->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
            ],
            Example::find([5, 7])->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '6', 'name' => 'Test6', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::all()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
            ],
            Example::where('age', 10)->get()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::where('age', [10, 40])->get()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::where('age', [10, 40])
                ->where('country', 'China', false)
                ->get()
                ->result()
                ->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '6', 'name' => 'Test6', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::query()->get()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '6', 'name' => 'Test6', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::where(function ($where) {
                $where->like('country', '%an%');
            })->get()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '1', 'name' => 'Test1', 'age' => '10', 'country' => 'China'],
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
            ],
            Example::where(function ($where) {
                $where->like('country', '%na%');
            })->get()->result()->toArray()
        );
        $this->assertEquals(1, Example::destroy(1));
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
                ['id' => '4', 'name' => 'Test4', 'age' => '10', 'country' => 'China'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '6', 'name' => 'Test6', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '8', 'name' => 'Test8', 'age' => '40', 'country' => 'China'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::all()->result()->toArray()
        );
        $this->assertEquals(3, Example::destroy([4, 6, 8]));
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '3', 'name' => 'Test3', 'age' => '20', 'country' => 'Germany'],
                ['id' => '5', 'name' => 'Test5', 'age' => '20', 'country' => 'Germany'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            Example::all()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
                ['id' => '10', 'name' => 'Test10', 'age' => '100', 'country' => 'China'],
            ],
            Example::transaction(function () {
                Example::create(['name' => 'Test10', 'age' => 100, 'country' => 'China']);
                Example::destroy([3, 5]);
                return Example::all()->result()->toArray();
            })
        );
    }

    public function testQuery()
    {
        $query = Example::where('name', ['Test2', 'Test7', 'Test9']);

        $this->assertTrue($query->exists());
        $this->assertEquals(3, $query->count());
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
                ['id' => '7', 'name' => 'Test7', 'age' => '40', 'country' => 'England'],
                ['id' => '9', 'name' => 'Test9', 'age' => '40', 'country' => 'Germany'],
            ],
            $query->get()->result()->toArray()
        );
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            $query->one()->result()->toArray()
        );
    }

    public function testStorage()
    {
        $storage = Example::find(2);

        $this->assertFalse($storage->isEmpty());
        $this->assertEquals(1, $storage->count());
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '20', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        $storage->set('age', 25)->save();
        $this->assertEquals(
            [
                ['id' => '2', 'name' => 'Test2', 'age' => '25', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        $storage->set('age', 35)->replace();
        $this->assertEquals(
            [
                ['id' => '11', 'name' => 'Test2', 'age' => '35', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        $storage->create(['name' => 'Test11', 'age' => '70', 'country' => 'China']);
        $this->assertEquals(
            [
                ['id' => '11', 'name' => 'Test2', 'age' => '35', 'country' => 'China'],
                ['id' => '12', 'name' => 'Test11', 'age' => '70', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        $storage->set('country', 'Germany')->save();
        $this->assertEquals(
            [
                ['id' => '11', 'name' => 'Test2', 'age' => '35', 'country' => 'Germany'],
                ['id' => '12', 'name' => 'Test11', 'age' => '70', 'country' => 'Germany'],
            ],
            $storage->result()->toArray()
        );

        $storage->set('country', 'China')->set('age', 5)->replace();
        $this->assertEquals(
            [
                ['id' => '13', 'name' => 'Test2', 'age' => '5', 'country' => 'China'],
                ['id' => '14', 'name' => 'Test11', 'age' => '5', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        $storage
            ->delete()
            ->set('name', 'Test12')->set('age', 12)->set('country', 'China')
            ->save();
        $this->assertEquals(
            [
                ['id' => '15', 'name' => 'Test12', 'age' => '12', 'country' => 'China'],
            ],
            $storage->result()->toArray()
        );

        Example::clean();
        $this->assertEquals(0, Example::count());
    }
}
