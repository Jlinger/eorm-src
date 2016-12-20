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

use Eorm\Library\Where;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
{
    public function testWhereGetArgument()
    {
        $this->assertEquals([], (new Where())->getArgument());
    }

    public function testWhereToString()
    {
        $this->assertEquals('', (new Where())->toString());
    }

    public function testWhereCompareEqual()
    {
        $where = (new Where())->compare('name', 'A');

        $this->assertEquals(['A'], $where->getArgument());
        $this->assertEquals('`name`=?', $where->toString());

        $where = (new Where())->compare('name', 'A', true);

        $this->assertEquals(['A'], $where->getArgument());
        $this->assertEquals('`name`=?', $where->toString());

        $where = (new Where())->compare('name', 'A', '=');

        $this->assertEquals(['A'], $where->getArgument());
        $this->assertEquals('`name`=?', $where->toString());
    }

    public function testWhereCompareNotEqual()
    {
        $where = (new Where())->compare('name', 'A', false);

        $this->assertEquals(['A'], $where->getArgument());
        $this->assertEquals('`name`!=?', $where->toString());

        $where = (new Where())->compare('name', 'A', '!=');

        $this->assertEquals(['A'], $where->getArgument());
        $this->assertEquals('`name`!=?', $where->toString());
    }

    public function testWhereCompareIsNull()
    {
        $where = (new Where())->compare('name', null);

        $this->assertEquals([], $where->getArgument());
        $this->assertEquals('`name` IS NULL', $where->toString());
    }

    public function testWhereCompareIsNotNull()
    {
        $where = (new Where())->compare('name', null, false);

        $this->assertEquals([], $where->getArgument());
        $this->assertEquals('`name` IS NOT NULL', $where->toString());
    }

    public function testWhereCompareInArray()
    {
        $where = (new Where())->compare('age', [1, 2, 3]);

        $this->assertEquals([1, 2, 3], $where->getArgument());
        $this->assertEquals('`age` IN (?,?,?)', $where->toString());
    }

    public function testWhereCompareNotInArray()
    {
        $where = (new Where())->compare('age', [1, 2, 3], false);

        $this->assertEquals([1, 2, 3], $where->getArgument());
        $this->assertEquals('`age` NOT IN (?,?,?)', $where->toString());
    }

    public function testWhereLike()
    {
        $where = (new Where())->like('name', '%ABC%');

        $this->assertEquals(['%ABC%'], $where->getArgument());
        $this->assertEquals('`name` LIKE ?', $where->toString());

        $where = (new Where())->like('name', '%DEF%', false);

        $this->assertEquals(['%DEF%'], $where->getArgument());
        $this->assertEquals('`name` NOT LIKE ?', $where->toString());
    }

    public function testWhereAndOr()
    {
        $where = new Where();

        $where
            ->compare('age', 10, '>=')
            ->compare('age', 20, '<=')
            ->compare('age', 13, false);

        $this->assertEquals([10, 20, 13], $where->getArgument());
        $this->assertEquals('`age`>=? AND `age`<=? AND `age`!=?', $where->toString());

        $where = new Where(false);

        $where
            ->compare('age', 10, '>=')
            ->compare('age', 20, '<=')
            ->compare('age', 13, false);

        $this->assertEquals([10, 20, 13], $where->getArgument());
        $this->assertEquals('`age`>=? OR `age`<=? OR `age`!=?', $where->toString());
    }

    public function testWhereGroup()
    {
        $where = new Where();
        $where
            ->compare('age', 10, '>')
            ->group(function ($where) {
                $where
                    ->compare('address', ['A', 'B'])
                    ->compare('num', 10);
            })
            ->compare('age', 20, '<=');

        $this->assertEquals([10, 'A', 'B', 10, 20], $where->getArgument());
        $this->assertEquals('`age`>? AND (`address` IN (?,?) OR `num`=?) AND `age`<=?', $where->toString());

        $where = new Where(false);
        $where
            ->compare('age', 10, '>')
            ->group(function ($where) {
                $where
                    ->compare('address', ['A', 'B'])
                    ->compare('num', 10);
            }, true)
            ->compare('age', 20, '<=');

        $this->assertEquals([10, 'A', 'B', 10, 20], $where->getArgument());
        $this->assertEquals('`age`>? OR (`address` IN (?,?) AND `num`=?) OR `age`<=?', $where->toString());
    }
}
