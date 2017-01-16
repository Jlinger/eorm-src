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
namespace Eorm\Library;

/**
 *
 */
class Argument
{
    /**
     * [$stack description]
     * @var array
     */
    protected $stack = [];

    /**
     * [__construct description]
     * @param array $values [description]
     */
    public function __construct($values = [])
    {
        $this->push($values);
    }

    /**
     * [push description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public function push($values)
    {
        foreach (Helper::toArray($values) as $value) {
            $this->stack[] = Helper::toScalar($value);
        }

        return $this;
    }

    /**
     * [count description]
     * @return [type] [description]
     */
    public function count()
    {
        return count($this->stack);
    }

    /**
     * [toArray description]
     * @return [type] [description]
     */
    public function toArray()
    {
        return $this->stack;
    }

    /**
     * [clean description]
     * @return [type] [description]
     */
    public function clean()
    {
        $this->stack = [];
        return $this;
    }
}
