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

class Query
{
    protected $actuator;
    protected $mode;
    protected $where   = null;
    protected $limit   = 0;
    protected $skip    = 0;
    protected $orderBy = [];

    public function __construct(Actuator $actuator, $mode = true)
    {
        $this->mode     = $mode;
        $this->actuator = $actuator;
    }

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

    public function orderBy($column, $ascend = true)
    {
        $formattedColumn = Helper::format($target);
        $order           = $ascend ? 'ASC' : 'DESC';

        $this->orderBy[$column] = "{$formattedColumn} {$order}";
        return $this;
    }

    public function limit($num)
    {
        $this->limit = intval($num);

        return $this;
    }

    public function skip($num)
    {
        $this->skip = intval($num);

        return $this;
    }

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

    public function one()
    {
        return $this->limit(1)->skip(0)->get();
    }

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
