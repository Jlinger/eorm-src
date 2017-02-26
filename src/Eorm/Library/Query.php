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
namespace Eorm\Library;

use Eorm\Library\Actuator;
use PDO;

/**
 *
 */
class Query
{
    /**
     * [$actuator description]
     * @var [type]
     */
    protected $actuator;

    /**
     * [$mode description]
     * @var [type]
     */
    protected $select;

    /**
     * [__construct description]
     * @param Actuator $actuator [description]
     * @param Select   $select   [description]
     */
    public function __construct(Actuator $actuator, Select $select)
    {
        $this->actuator = $actuator;
        $this->select   = $select;
    }

    /**
     * [where description]
     * @param  [type]  $target [description]
     * @param  [type]  $value  [description]
     * @param  boolean $option [description]
     * @return [type]          [description]
     */
    public function where($target, $value = null, $option = true)
    {
        $this->select->where($target, $value, $option);

        return $this;
    }

    /**
     * [orderBy description]
     * @param  [type]  $field  [description]
     * @param  boolean $ascend [description]
     * @return [type]          [description]
     */
    public function orderBy($field, $ascend = true)
    {
        $this->select->orderBy($field, $ascend);

        return $this;
    }

    /**
     * [limit description]
     * @param  [type] $count [description]
     * @return [type]      [description]
     */
    public function limit($count)
    {
        $this->select->limit($count);

        return $this;
    }

    /**
     * [skip description]
     * @param  [type] $count [description]
     * @return [type]      [description]
     */
    public function skip($count)
    {
        $this->select->skip($count);

        return $this;
    }

    /**
     * [get description]
     * @return [type] [description]
     */
    public function get(array $fields = [])
    {
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                if (is_numeric($key)) {
                    $this->select->field($value);
                } else {
                    $this->select->field($value, $key);
                }
            }
        }

        return new Storage(
            $this->actuator->query(
                $this->select->build(),
                $this->parameter()->toArray()
            ),
            $this->actuator
        );
    }

    /**
     * [one description]
     * @return [type] [description]
     */
    public function one(array $fields = [])
    {
        return $this->limit(1)->skip(0)->get($fields);
    }

    /**
     * [count description]
     * @return [type] [description]
     */
    public function count()
    {
        $field    = $this->actuator->getPrimaryKey();
        $table    = $this->actuator->getTable();
        $sql      = "SELECT COUNT($field) AS `total` FROM {$table}";
        $argument = null;

        if ($this->where) {
            $where = $this->where->toString();
            if ($where) {
                $sql      = "{$sql} WHERE {$where}";
                $argument = new Argument($this->where->getArgument());
            }
        }

        return intval(
            $this->actuator
                ->fetch($sql, $argument)
                ->fetchAll(PDO::FETCH_ASSOC)[0]['total']
        );
    }

    /**
     * [exists description]
     * @return [type] [description]
     */
    public function exist()
    {
        $field    = $this->actuator->getPrimaryKey();
        $table    = $this->actuator->getTable();
        $sql      = "SELECT {$field} FROM {$table}";
        $argument = null;

        if ($this->where) {
            $where = $this->where->toString();
            if ($where) {
                $sql      = "{$sql} WHERE {$where}";
                $argument = new Argument($this->where->getArgument());
            }
        }

        return boolval(
            $this->actuator
                ->fetch("SELECT EXISTS({$sql} LIMIT 1) AS `has`", $argument)
                ->fetchAll(PDO::FETCH_ASSOC)[0]['has']
        );
    }

}
