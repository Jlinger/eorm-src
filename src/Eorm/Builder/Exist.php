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

/**
 *
 */
class Exist extends Select
{
    /**
     * [$type description]
     *
     * @var string
     */
    protected static $type = 'exist';

    /**
     * [build description]
     *
     * @return string
     */
    public function build()
    {
        $table = $this->actuator()->table();
        $filed = $this->actuator()->primaryKey();

        $statement = "SELECT {$filed} FROM {$table}";
        if ($this->where) {
            $statement .= ' WHERE ' . $this->where->build();
        }

        return "SELECT EXISTS({$statement} LIMIT 1) AS `eorm_exist`";
    }
}
