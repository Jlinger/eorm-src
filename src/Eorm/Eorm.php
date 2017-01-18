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
namespace Eorm;

use Closure;
use Eorm\Library\Actuator;
use Eorm\Library\Argument;
use Eorm\Library\Builder;
use Eorm\Library\Helper;
use Eorm\Library\Query;
use Eorm\Library\Storage;
use Eorm\Library\Where;
use PDO;

class Eorm
{
    private static $actuators = [];

    protected $table      = null;
    protected $primaryKey = 'id';
    protected $server     = 'default';

    private static function getActuator()
    {
        // Model class name.
        $abstract = static::class;

        if (!isset(self::$actuators[$abstract])) {

            list($server, $table, $primaryKey) = call_user_func(function ($abstract) {
                $reflection = new \ReflectionClass($abstract);
                $props      = [];
                foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $prop) {
                    $name = strtolower($prop->getName());
                    if (in_array($name, ['table', 'primarykey', 'server'])) {
                        $prop->setAccessible(true);
                        $props[$name] = $prop;
                    }
                }

                $instanse = new $abstract();
                $values   = [];
                foreach ($props as $name => $prop) {
                    $values[$name] = $prop->getValue($instanse);
                }

                if (is_null($values['table'])) {
                    $temp            = explode('\\', $abstract);
                    $values['table'] = strtolower(end($temp));
                }

                return [$values['server'], $values['table'], $values['primarykey']];
            }, $abstract);

            // Create model actuator instanse.
            self::$actuators[$abstract] = new Actuator($server, $table, $primaryKey);
        }

        return self::$actuators[$abstract];
    }

    private static function getWhere($target, $value, $option, $mode)
    {
        if (is_bool($target)) {
            $where = new Where($target);
        } elseif ($target instanceof Closure) {
            $where = new Where(is_bool($value) ? $value : true);
            $target($where);
        } else {
            $where = new Where($mode);
            $where->compare($target, $value, $option);
        }

        return $where;
    }

    public static function getTable()
    {
        return self::getActuator()->getTable(false);
    }

    public static function getPrimaryKey()
    {
        return self::getActuator()->getPrimaryKey(false);
    }

    public static function where($target, $value = null, $option = true, $mode = true)
    {
        return static::query($mode)->where($target, $value, $option);
    }

    public static function find($ids)
    {
        $actuator = self::getActuator();
        $argument = new Argument($ids);
        $count    = $argument->count();
        $table    = $actuator->getTable();
        $where    = Builder::makeWhereIn($actuator->getPrimaryKey(false), $count);

        return new Storage(
            $actuator->fetch("SELECT * FROM {$table} WHERE {$where} LIMIT {$count}", $argument),
            $actuator
        );
    }

    public static function query($mode = true)
    {
        return new Query(self::getActuator(), $mode);
    }

    public static function all()
    {
        $actuator = self::getActuator();
        $table    = $actuator->getTable();

        return new Storage(
            $actuator->fetch("SELECT * FROM {$table}"),
            $actuator
        );
    }

    public static function create(array $columns)
    {
        $actuator = self::getActuator();
        $field    = Builder::makeField(array_keys($columns));
        $table    = $actuator->getTable();
        $columns  = Builder::normalizeInsertRows(array_values($columns));
        $rowCount = count(reset($columns));
        $argument = new Argument();
        $unit     = Helper::fill(count($columns));
        $values   = implode(',', array_map(function (...$row) use ($argument, $unit) {
            $argument->push($row);
            return $unit;
        }, ...$columns));

        $actuator->fetch("INSERT INTO {$table} ({$field}) VALUES {$values}", $argument);

        $ids   = Helper::range($actuator->lastId(), $rowCount);
        $count = $argument->clean()->push($ids)->count();
        $where = Builder::makeWhereIn($actuator->getPrimaryKey(false), $count);

        return new Storage(
            $actuator->fetch("SELECT * FROM {$table} WHERE {$where} LIMIT {$count}", $argument),
            $actuator
        );
    }

    public static function insert(array $columns)
    {
        $actuator = self::getActuator();
        $field    = Builder::makeField(array_keys($columns));
        $table    = $actuator->getTable();
        $columns  = Builder::normalizeInsertRows(array_values($columns));
        $rowCount = count(reset($columns));
        $argument = new Argument();
        $unit     = Helper::fill(count($columns));
        $values   = implode(',', array_map(function (...$row) use ($argument, $unit) {
            $argument->push($row);
            return $unit;
        }, ...$columns));

        $actuator->fetch("INSERT INTO {$table} ({$field}) VALUES {$values}", $argument);

        return Helper::range($actuator->lastId(), $rowCount);
    }

    public static function count($column = null, $distinct = false)
    {
        $actuator = self::getActuator();
        $table    = $actuator->getTable();
        $field    = Builder::makeCountField(
            is_null($column) ? $actuator->getPrimaryKey(false) : $column,
            $distinct
        );

        return intval(
            $actuator
                ->fetch("SELECT {$field} FROM {$table}")
                ->fetchAll(PDO::FETCH_ASSOC)[0]['total']
        );
    }

    public static function destroy($ids)
    {
        $actuator = self::getActuator();
        $table    = $actuator->getTable();
        $argument = new Argument($ids);
        $count    = $argument->count();
        $where    = Builder::makeWhereIn($actuator->getPrimaryKey(false), $count);

        return $actuator
            ->fetch("DELETE FROM {$table} WHERE {$where} LIMIT {$count}", $argument)
            ->rowCount();
    }

    public static function clean()
    {
        $actuator = self::getActuator();
        $actuator->fetch('TRUNCATE TABLE ' . $actuator->getTable());

        return true;
    }

    public static function transaction(Closure $closure, $option = null)
    {
        return self::getActuator()->transaction($closure, $option);
    }
}
