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

use Eorm\Contracts\ActuatorInterface;

/**
 *
 */
class Select extends AnotherClass
{
    protected $actuator;
    protected $table;
    protected $primaryKey;
    protected $where = null;

    public function __construct(ActuatorInterface $actuator)
    {
        $this->actuator   = $actuator;
        $this->table      = $actuator->table();
        $this->primaryKey = $actuator->primaryKey();
    }

    public function where($target, $value = null, $option = true, $mode = true)
    {

    }

    public function orderBy($field, $ascend = true)
    {

    }

    public function limit($count)
    {

    }

    public function skip($count)
    {

    }

    public function fetch()
    {

    }

    public function one()
    {

    }

    public function count()
    {

    }

    public function exists()
    {

    }
}
