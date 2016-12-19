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

use Closure;
use Eorm\Library\Where;
use Eorm\Server;
use PDO;

class Query
{
    protected $where;
    protected $table;
    protected $primaryKey;
    protected $limit = 0;
    protected $skip  = 0;

    public function __construct(Where $where, $table, $primaryKey)
    {
        $this->where      = $where;
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
    }

    public function where($target, $value = null, $option = true)
    {
        if ($target instanceof Closure) {
            if (is_bool($value)) {
                $this->where->group($target, $value);
            } else {
                $target($this->where);
            }
        } else {
            $this->where->compare($target, $value, $option);
        }

        return $this;
    }

    public function limit($num)
    {
        $this->limit = (int) $num;
        return $this;
    }

    public function skip($num)
    {
        $this->skip = (int) $num;
        return $this;
    }

    public function get()
    {
        $table    = Helper::standardise($this->table);
        $argument = new Argument();
        $sql      = "SELECT * FROM {$table}";

        if ($where = $this->where->toString()) {
            $sql = "{$sql} WHERE {$where}";
            $argument->push($this->where->getArgument());
        }

        if ($this->limit) {
            if ($this->skip) {
                $sql = "{$sql} LIMIT {$this->skip},{$this->limit}";
            } else {
                $sql = "{$sql} LIMIT {$this->limit}";
            }
        }

        return new Storage(
            Server::execute($sql, $argument)->fetchAll(PDO::FETCH_ASSOC),
            $this->table,
            $this->primaryKey
        );
    }

    public function one()
    {
        return $this->limit(1)->skip(0)->get();
    }

    public function count()
    {
        $field    = Helper::standardise($this->primaryKey);
        $table    = Helper::standardise($this->table);
        $sql      = "SELECT COUNT($field) AS `total` FROM {$table}";
        $argument = new Argument();

        if ($where = $this->where->toString()) {
            $sql = "{$sql} WHERE {$where}";
            $argument->push($this->where->getArgument());
        }

        return (int) Server::execute($sql, $argument)->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
    }

    public function exists()
    {
        $field    = Helper::standardise($this->primaryKey);
        $table    = Helper::standardise($this->table);
        $sql      = "SELECT {$field} FROM {$table}";
        $argument = new Argument();

        if ($where = $this->where->toString()) {
            $sql = "{$sql} WHERE {$where}";
            $argument->push($this->where->getArgument());
        }

        return (bool) Server::execute(
            "SELECT EXISTS({$sql}) AS `has`",
            $argument
        )->fetchAll(PDO::FETCH_ASSOC)[0]['has'];
    }
}
