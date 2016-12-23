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

use Eorm\Exceptions\EormException;
use Eorm\Server;
use PDO;

class Storage
{
    protected $source;
    protected $table;
    protected $primaryKey;
    protected $server;
    protected $primaryKeys = [];
    protected $changes     = [];

    public function __construct(array $source, $table, $primaryKey, $server)
    {
        $this->source     = $source;
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
        $this->server     = $server;

        if (!empty($source)) {
            $this->primaryKeys = array_column($source, $primaryKey);
        }
    }

    public function result()
    {
        return new Result($this->source);
    }

    public function isEmpty()
    {
        return empty($this->source);
    }

    public function count()
    {
        return count($this->source);
    }

    public function set($target, $value = null)
    {
        if (is_array($target)) {
            foreach ($target as $k => $v) {
                $this->changes[$k] = $v;
            }
        } else {
            $this->changes[$target] = $value;
        }

        return $this;
    }

    public function save()
    {
        if (empty($this->changes)) {
            return $this;
        }

        if ($length = count($this->primaryKeys)) {
            $table   = Helper::standardise($this->table);
            $where   = Helper::makeWhereWithPrimaryKey($this->primaryKey, $length);
            $changes = implode(',', array_map(function ($field) {
                return Helper::standardise($field) . ' = ?';
            }, array_keys($this->changes)));

            Server::execute(
                $this->server,
                "UPDATE {$table} SET {$changes} WHERE {$where} LIMIT {$length}",
                (new Argument($this->changes))->push($this->primaryKeys)
            );

            $this->changes = [];
            return $this->reload();
        } else {
            return $this->create($this->changes);
        }
    }

    public function replace()
    {
        if (empty($this->changes)) {
            return $this;
        }

        if ($length = count($this->primaryKeys)) {
            $source = array_map(
                function ($row) {
                    unset($row[$this->primaryKey]);
                    foreach ($this->changes as $column => $value) {
                        $row[$column] = $value;
                    }
                    return $row;
                },
                $this->source
            );

            $rows     = count($source);
            $columns  = count(reset($source));
            $field    = Helper::mergeField(array_keys(reset($source)));
            $table    = Helper::standardise($this->table);
            $values   = Helper::fill($rows, Helper::fill($columns), false);
            $argument = new Argument();

            foreach ($source as $row) {
                $argument->push($row);
            }

            $this->changes = [];
            $this->delete();

            Server::execute(
                $this->server,
                "INSERT INTO {$table} ({$field}) VALUES {$values}",
                $argument
            );

            $this->primaryKeys = Helper::range((int) Server::insertId($this->server), $rows, true);

            return $this->reload();
        } else {
            return $this->create($this->changes);
        }
    }

    public function reload()
    {
        if ($length = count($this->primaryKeys)) {
            $table = Helper::standardise($this->table);
            $where = Helper::makeWhereWithPrimaryKey($this->primaryKey, $length);

            $this->source = Server::execute(
                $this->server,
                "SELECT * FROM {$table} WHERE {$where} LIMIT {$length}",
                new Argument($this->primaryKeys)
            )->fetchAll(PDO::FETCH_ASSOC);

            if ($this->isEmpty()) {
                $this->primaryKeys = [];
            } else {
                $this->primaryKeys = array_column($this->source, $this->primaryKey);
            }
        }

        return $this;
    }

    public function delete()
    {
        if ($length = count($this->primaryKeys)) {
            $table = Helper::standardise($this->table);
            $where = Helper::makeWhereWithPrimaryKey($this->primaryKey, $length);

            Server::execute(
                $this->server,
                "DELETE FROM {$table} WHERE {$where} LIMIT {$length}",
                new Argument($this->primaryKeys)
            );

            $this->primaryKeys = [];
        }

        return $this;
    }

    public function create(array $data)
    {
        if (empty($data)) {
            throw new EormException('The database table cannot be inserted into the empty data.');
        }

        $field = Helper::mergeField(array_keys($data));
        $table = Helper::standardise($this->table);

        list($argument, $rows, $columns) = Helper::makeInsertArray(array_values($data));

        $values = Helper::fill($rows, Helper::fill($columns), false);

        Server::execute(
            $this->server,
            "INSERT INTO {$table} ({$field}) VALUES {$values}",
            $argument
        );

        $this->primaryKeys = Helper::merge(
            Helper::range((int) Server::insertId($this->server), $rows, true),
            $this->primaryKeys
        );

        return $this->reload();
    }
}
