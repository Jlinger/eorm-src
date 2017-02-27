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
namespace Eorm\Builder;

use Eorm\Builder\Foundation\BuilderAbstract;
use Eorm\Builder\Foundation\Traits\Astriction;
use Eorm\Builder\Foundation\Traits\Condition;

/**
 *
 */
class Delete extends BuilderAbstract
{
    use Condition, Astriction;

    /**
     * [$type description]
     *
     * @var string
     */
    protected static $type = 'delete';

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        $statement = 'DELETE FROM ' . $this->actuator()->table();
        if ($this->where) {
            $statement .= ' WHERE ' . $this->where->build();
        }
        if ($this->limit) {
            $statement .= ' LIMIT ' . $this->limit;
        }

        return $statement;
    }
}
