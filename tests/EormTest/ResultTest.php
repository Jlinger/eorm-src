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

use Eorm\Library\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testResultIsEmpty()
    {
        $result1 = new Result([]);
        $result2 = new Result([['id' => '1']]);

        $this->assertTrue($result1->isEmpty());
        $this->assertFalse($result2->isEmpty());
    }

    public function testResultCount()
    {
        $result1 = new Result([]);
        $result2 = new Result([['id' => '1']]);
        $result3 = new Result([['id' => '1'], ['id' => '2']]);

        $this->assertEquals(0, $result1->count());
        $this->assertEquals(1, $result2->count());
        $this->assertEquals(2, $result3->count());
    }

    public function testResultToArray()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A'],
            ['id' => '2', 'name' => 'B'],
            ['id' => '3', 'name' => 'C'],
        ]);

        $this->assertEquals([
            ['id' => '1', 'name' => 'A'],
            ['id' => '2', 'name' => 'B'],
            ['id' => '3', 'name' => 'C'],
        ], $result->toArray());
        $this->assertEquals([
            ['name' => 'A'],
            ['name' => 'B'],
            ['name' => 'C'],
        ], $result->toArray('name'));
        $this->assertEquals([
            ['name' => 'A', 'uname' => 'A'],
            ['name' => 'B', 'uname' => 'B'],
            ['name' => 'C', 'uname' => 'C'],
        ], $result->toArray(['name', 'uname' => 'name']));
        $this->assertEquals([
            ['u_id' => '1', 'u_name' => 'A'],
            ['u_id' => '2', 'u_name' => 'B'],
            ['u_id' => '3', 'u_name' => 'C'],
        ], $result->toArray(function ($name) {
            return 'u_' . $name;
        }));
        $this->assertEquals([
            ['u_id' => '1', 'u_name' => 'A', 'n_id' => '1', 'n_name' => 'A'],
            ['u_id' => '2', 'u_name' => 'B', 'n_id' => '2', 'n_name' => 'B'],
            ['u_id' => '3', 'u_name' => 'C', 'n_id' => '3', 'n_name' => 'C'],
        ], $result->toArray(function ($name) {
            return ['u_' . $name, 'n_' . $name];
        }));
    }

    public function testResultFilter()
    {
        $result = new Result([
            ['id' => '1'],
            ['id' => '2'],
            ['id' => '3'],
        ]);

        $result->filter(function ($row, $max) {
            return intval($row['id']) > $max;
        }, 1);

        $this->assertEquals([
            ['id' => '2'],
            ['id' => '3'],
        ], $result->toArray());

        $result->filter(function ($row) {
            return intval($row['id']) > 2;
        });

        $this->assertEquals([
            ['id' => '3'],
        ], $result->toArray());
    }

    public function testResultEach()
    {
        $result = new Result([
            ['id' => '1'],
            ['id' => '2'],
            ['id' => '3'],
        ]);

        $data = $result->each(function ($row, $map) {
            $row['name'] = $map[$row['id']];
            return $row;
        }, ['1' => 'A', '2' => 'B', '3' => 'C']);

        $this->assertEquals([
            ['id' => '1', 'name' => 'A'],
            ['id' => '2', 'name' => 'B'],
            ['id' => '3', 'name' => 'C'],
        ], $data);

        $data = $result->each(function ($row) {
            return true;
        });

        $this->assertEquals([true, true, true], $data);
    }

    public function testResultMap()
    {
        $result = (new Result([
            ['id' => '1'],
            ['id' => '2'],
            ['id' => '3'],
        ]))->map([
            '1' => 'A',
            '2' => 'B',
        ], 'id');

        $this->assertEquals([
            ['id' => 'A'],
            ['id' => 'B'],
            ['id' => null],
        ], $result->toArray());

        $result = (new Result([
            ['id' => '1'],
            ['id' => '2'],
            ['id' => '3'],
        ]))->map([
            '1' => 10,
            '2' => 20,
        ], 'age', 'id');

        $this->assertEquals([
            ['id' => '1', 'age' => 10],
            ['id' => '2', 'age' => 20],
            ['id' => '3', 'age' => null],
        ], $result->toArray());

        $result = (new Result([
            ['id' => '1'],
            ['id' => '2'],
            ['id' => '3'],
        ]))->map([
            '1' => 10,
            '2' => 20,
        ], 'age', 'id', 0);

        $this->assertEquals([
            ['id' => '1', 'age' => 10],
            ['id' => '2', 'age' => 20],
            ['id' => '3', 'age' => 0],
        ], $result->toArray());
    }

    public function testResultGroup()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A', 'age' => 10],
            ['id' => '2', 'name' => 'A', 'age' => 20],
            ['id' => '3', 'name' => 'A', 'age' => 20],
        ]);

        $this->assertEquals([
            10 => [
                ['id' => '1', 'name' => 'A', 'age' => 10],
            ],
            20 => [
                ['id' => '2', 'name' => 'A', 'age' => 20],
                ['id' => '3', 'name' => 'A', 'age' => 20],
            ],
        ], $result->group('age'));

        $this->assertEquals([
            10 => ['1'],
            20 => ['2', '3'],
        ], $result->group('age', 'id'));

        $this->assertEquals([
            10 => [
                ['id' => '1', 'name' => 'A'],
            ],
            20 => [
                ['id' => '2', 'name' => 'A'],
                ['id' => '3', 'name' => 'A'],
            ],
        ], $result->group('age', ['id', 'name']));

        $this->assertEquals([
            10 => [
                ['uname' => 'A'],
            ],
            20 => [
                ['uname' => 'A'],
                ['uname' => 'A'],
            ],
        ], $result->group('age', ['uname' => 'name']));

        $this->assertEquals([
            10 => [
                ['u_id' => '1', 'u_name' => 'A', 'u_age' => 10],
            ],
            20 => [
                ['u_id' => '2', 'u_name' => 'A', 'u_age' => 20],
                ['u_id' => '3', 'u_name' => 'A', 'u_age' => 20],
            ],
        ], $result->group('age', function ($name) {
            return 'u_' . $name;
        }));

        $this->assertEquals([
            10 => [
                ['u_id' => '1', 'u_name' => 'A', 'u_age' => 10, 'n_id' => '1', 'n_name' => 'A', 'n_age' => 10],
            ],
            20 => [
                ['u_id' => '2', 'u_name' => 'A', 'u_age' => 20, 'n_id' => '2', 'n_name' => 'A', 'n_age' => 20],
                ['u_id' => '3', 'u_name' => 'A', 'u_age' => 20, 'n_id' => '3', 'n_name' => 'A', 'n_age' => 20],
            ],
        ], $result->group('age', function ($name) {
            return ['u_' . $name, 'n_' . $name];
        }));
    }

    public function testResultTranspose()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A', 'age' => 10],
            ['id' => '2', 'name' => 'B', 'age' => 20],
            ['id' => '3', 'name' => 'C', 'age' => 30],
        ]);

        $this->assertEquals([
            'id'   => ['1', '2', '3'],
            'name' => ['A', 'B', 'C'],
            'age'  => [10, 20, 30],
        ], $result->transpose());
    }

    public function testResultTransform()
    {
        $result = new Result([
            ['money' => '10.5', 'num' => '5', 'hot' => 1, 'tag' => 'M'],
            ['money' => '100.22', 'num' => '6', 'hot' => 0, 'tag' => 'S'],
            ['money' => '3.12', 'num' => '2', 'hot' => 1, 'tag' => 'S'],
        ]);

        $this->assertEquals([
            ['money' => 10.5, 'num' => 5, 'hot' => true, 'tag' => ['M']],
            ['money' => 100.22, 'num' => 6, 'hot' => false, 'tag' => ['S']],
            ['money' => 3.12, 'num' => 2, 'hot' => true, 'tag' => ['S']],
        ], $result->transform([
            'money' => 'float',
            'num'   => 'integer',
            'hot'   => 'boolean',
            'tag'   => 'array',
        ])->toArray());
    }

    public function testResultRow()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A', 'age' => 10],
            ['id' => '2', 'name' => 'B', 'age' => 20],
            ['id' => '3', 'name' => 'C', 'age' => 30],
        ]);

        $this->assertEquals(['id' => '2', 'name' => 'B', 'age' => 20], $result->row(1));
        $this->assertEquals(['id' => '2', 'name' => 'B', 'age' => 20], $result->row(-2));
    }

    public function testResultFirst()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A', 'age' => 10],
            ['id' => '2', 'name' => 'B', 'age' => 20],
            ['id' => '3', 'name' => 'C', 'age' => 30],
        ]);

        $this->assertEquals(['id' => '1', 'name' => 'A', 'age' => 10], $result->first());
    }

    public function testResultLast()
    {
        $result = new Result([
            ['id' => '1', 'name' => 'A', 'age' => 10],
            ['id' => '2', 'name' => 'B', 'age' => 20],
            ['id' => '3', 'name' => 'C', 'age' => 30],
        ]);

        $this->assertEquals(['id' => '3', 'name' => 'C', 'age' => 30], $result->last());
    }

    public function testResultColumn()
    {
        $result = new Result([
            ['name' => 'A', 'age' => 10],
            ['name' => 'B', 'age' => 20],
            ['name' => 'C', 'age' => 20],
        ]);

        $this->assertEquals(['A', 'B', 'C'], $result->column('name'));
        $this->assertEquals([10, 20], $result->column('age', true));
    }

    public function testResultMagicMethods()
    {
        $result = new Result([
            ['name' => 'A'],
            ['name' => 'B'],
        ]);

        $this->assertEquals(['A', 'B'], $result->name);
        $this->assertTrue(isset($result->name));
        $this->assertFalse(isset($result->age));

        $result = new Result([['name' => 'A']]);

        $this->assertEquals('A', $result->name);
    }
}
