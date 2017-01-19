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

use Closure;
use Eorm\Library\Actuator;
use InvalidArgumentException;
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
    protected $mode;

    /**
     * [$where description]
     * @var null
     */
    protected $where = null;

    /**
     * [$limit description]
     * @var integer
     */
    protected $limit = 0;

    /**
     * [$skip description]
     * @var integer
     */
    protected $skip = 0;

    /**
     * [$orderBy description]
     * @var array
     */
    protected $orderBy = [];

    /**
     * [__construct description]
     * @param Actuator $actuator [description]
     * @param boolean  $mode     [description]
     */
    public function __construct(Actuator $actuator, $mode = true)
    {
        $this->mode     = $mode;
        $this->actuator = $actuator;
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
        if (is_null($this->where)) {
            $this->where = new Where($this->mode);
        }

        if (is_string($target)) {
            $this->where->compare($target, $value, $option);
        } elseif ($target instanceof Closure) {
            if (is_bool($value)) {
                $this->where->group($target, $value);
            } else {
                $target($this->where);
            }
        } else {
            throw new InvalidArgumentException("The condition target must be a string or a closure.");
        }

        return $this;
    }

    /**
     * [orderBy description]
     * @param  [type]  $column [description]
     * @param  boolean $ascend [description]
     * @return [type]          [description]
     */
    public function orderBy($column, $ascend = true)
    {
        $formattedColumn = Helper::format($target);
        $order           = $ascend ? 'ASC' : 'DESC';

        $this->orderBy[$column] = "{$formattedColumn} {$order}";
        return $this;
    }

    /**
     * [limit description]
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    public function limit($num)
    {
        $this->limit = intval($num);

        return $this;
    }

    /**
     * [skip description]
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    public function skip($num)
    {
        $this->skip = intval($num);

        return $this;
    }

    /**
     * [get description]
     * @return [type] [description]
     */
    public function get()
    {
        $table    = $this->actuator->getTable();
        $sql      = "SELECT * FROM {$table}";
        $argument = null;

        if ($this->where) {
            $where = $this->where->toString();
            if ($where) {
                $sql      = "{$sql} WHERE {$where}";
                $argument = new Argument($this->where->getArgument());
            }
        }

        if (!empty($this->orderBy)) {
            $orderBy = Helper::join($this->orderBy);
            $sql     = "{$sql} ORDER BY {$orderBy}";
        }

        if ($this->limit) {
            if ($this->skip) {
                $sql = "{$sql} LIMIT {$this->skip},{$this->limit}";
            } else {
                $sql = "{$sql} LIMIT {$this->limit}";
            }
        }

        return new Storage($this->actuator->fetch($sql, $argument), $this->actuator);
    }

    /**
     * [one description]
     * @return [type] [description]
     */
    public function one()
    {
        return $this->limit(1)->skip(0)->get();
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
    public function exists()
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
