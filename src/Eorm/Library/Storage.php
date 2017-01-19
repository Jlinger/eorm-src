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

use PDO;
use PDOStatement;

/**
 *
 */
class Storage
{
    /**
     * [$actuator description]
     * @var [type]
     */
    protected $actuator;

    /**
     * [$source description]
     * @var array
     */
    protected $source = [];

    /**
     * [$ids description]
     * @var array
     */
    protected $ids = [];

    /**
     * [$changes description]
     * @var array
     */
    protected $changes = [];

    /**
     * [__construct description]
     * @param PDOStatement $statement [description]
     * @param Actuator     $actuator  [description]
     */
    public function __construct(PDOStatement $statement, Actuator $actuator)
    {
        $this->source   = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->actuator = $actuator;

        if (!$this->isEmpty()) {
            $this->ids = array_column($this->source, $actuator->getPrimaryKey(false));
        }
    }

    /**
     * [result description]
     * @return [type] [description]
     */
    public function result()
    {
        return new Result($this->source);
    }

    /**
     * [isEmpty description]
     * @return boolean [description]
     */
    public function isEmpty()
    {
        return empty($this->source);
    }

    /**
     * [count description]
     * @return [type] [description]
     */
    public function count()
    {
        return count($this->source);
    }

    /**
     * [set description]
     * @param [type] $column [description]
     * @param [type] $target [description]
     */
    public function set($column, $target = null)
    {
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->changes[$key] = $value;
            }
        } else {
            $this->changes[$column] = $target;
        }

        return $this;
    }

    /**
     * [save description]
     * @param  boolean $create [description]
     * @return [type]          [description]
     */
    public function save($create = false)
    {
        if (empty($this->changes)) {
            return $this;
        }

        $count = count($this->ids);

        if ($count) {
            $table    = $this->actuator->getTable();
            $where    = Builder::makeWhereIn($this->actuator->getPrimaryKey(false), $count);
            $argument = new Argument($this->changes);
            $changes  = implode(',', array_map(
                function ($column) {
                    return Helper::format($column) . '=?';
                },
                array_keys($this->changes)
            ));

            $this->actuator->fetch(
                "UPDATE {$table} SET {$changes} WHERE {$where} LIMIT {$count}",
                $argument->push($this->ids)
            );

            $this->changes = [];
            return $this->reload();
        } else {
            if ($create) {
                $this->insert($this->changes);
            }

            $this->changes = [];
            return $this;
        }
    }

    /**
     * [replace description]
     * @param  boolean $create [description]
     * @return [type]          [description]
     */
    public function replace($create = false)
    {
        if (empty($this->changes)) {
            return $this;
        }

        $count = count($this->ids);

        if ($count) {
            $primaryKey = $this->actuator->getPrimaryKey(false);
            $source     = array_map(
                function ($row) use ($primaryKey) {
                    foreach ($this->changes as $column => $value) {
                        $row[$column] = $value;
                    }

                    unset($row[$primaryKey]);

                    return $row;
                },
                $this->source
            );

            $field    = Builder::makeField(array_keys(reset($source)));
            $table    = $this->actuator->getTable();
            $rowCount = count($source);
            $values   = Helper::fill($rowCount, Helper::fill(count(reset($source))), false);
            $argument = new Argument($this->ids);
            $where    = Builder::makeWhereIn($primaryKey, $count);

            $this->actuator->fetch("DELETE FROM {$table} WHERE {$where} LIMIT {$count}", $argument);

            $argument->clean();
            foreach ($source as $row) {
                $argument->push($row);
            }

            $this->actuator->fetch("INSERT INTO {$table} ({$field}) VALUES {$values}", $argument);

            $this->ids = Helper::range($this->actuator->lastId(), $rowCount);

            return $this->reload();
        } else {
            if ($create) {
                $this->insert($this->changes);
            }

            $this->changes = [];
            return $this;
        }
    }

    /**
     * [reload description]
     * @return [type] [description]
     */
    public function reload()
    {
        $count = count($this->ids);

        if ($count) {
            $table = $this->actuator->getTable();
            $where = Builder::makeWhereIn($this->actuator->getPrimaryKey(false), $count);

            $this->source = $this
                ->actuator
                ->fetch("SELECT * FROM {$table} WHERE {$where} LIMIT {$count}", new Argument($this->ids))
                ->fetchAll(PDO::FETCH_ASSOC);

            if ($this->isEmpty()) {
                $this->ids = [];
            } else {
                $this->ids = array_column($this->source, $this->actuator->getPrimaryKey(false));
            }
        } else {
            $this->source = [];
        }

        return $this;
    }

    /**
     * [delete description]
     * @return [type] [description]
     */
    public function delete()
    {
        $count = count($this->ids);

        if ($count) {
            $table = $this->actuator->getTable();
            $where = Builder::makeWhereIn($this->actuator->getPrimaryKey(false), $count);

            $this->actuator->fetch(
                "DELETE FROM {$table} WHERE {$where} LIMIT {$count}",
                new Argument($this->ids)
            );

            $this->ids = [];
        }

        return $this;
    }

    /**
     * [insert description]
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
    public function insert(array $columns)
    {
        $field    = Builder::makeField(array_keys($columns));
        $table    = $this->actuator->getTable();
        $columns  = Builder::normalizeInsertRows(array_values($columns));
        $rowCount = count(reset($columns));
        $argument = new Argument();
        $unit     = Helper::fill(count($columns));
        $values   = implode(',', array_map(function (...$row) use ($argument, $unit) {
            $argument->push($row);
            return $unit;
        }, ...$columns));

        $this->actuator->fetch("INSERT INTO {$table} ({$field}) VALUES {$values}", $argument);

        return $this->push(Helper::range($this->actuator->lastId(), $rowCount));
    }

    /**
     * [push description]
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function push($ids)
    {
        array_push($this->ids, ...Helper::toArray($ids));

        return $this->reload();
    }
}
