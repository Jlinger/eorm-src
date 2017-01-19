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
namespace Eorm;

use Closure;
use Eorm\Library\Actuator;
use Eorm\Library\Argument;
use Eorm\Library\Builder;
use Eorm\Library\Helper;
use Eorm\Library\Query;
use Eorm\Library\Storage;
use Eorm\Library\Where;

/**
 * Eorm Model Base Class.
 */
class Eorm
{
    /**
     * All model actuator instanses.
     *
     * @var array
     */
    private static $actuators = [];

    /**
     * Default table name.
     *
     * @var null
     */
    protected $table = null;

    /**
     * Default table primary key name.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Default server name.
     *
     * @var string
     */
    protected $server = 'default';

    /**
     * Create/Get actuator instanse.
     *
     * @return Actuator
     */
    private static function getActuator()
    {
        $abstract = static::class;
        if (!isset(self::$actuators[$abstract])) {
            self::$actuators[$abstract] = new Actuator($abstract);
        }

        return self::$actuators[$abstract];
    }

    /**
     * Get table name.
     *
     * @return string
     */
    public static function getTable()
    {
        return self::getActuator()->getTable(false);
    }

    /**
     * Get table primary key name.
     *
     * @return string
     */
    public static function getPrimaryKey()
    {
        return self::getActuator()->getPrimaryKey(false);
    }

    /**
     * Create a Query instanse, and set a SQL where condition.
     *
     * @param  string|Closure  $target  The field name.
     * @param  mixed           $value   The condition value.
     * @param  boolean|string  $option  The connector.
     * @param  boolean         $mode    The where mode.
     * @return Query
     */
    public static function where($target, $value = null, $option = true, $mode = true)
    {
        return static::query($mode)->where($target, $value, $option);
    }

    /**
     * [find description]
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
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

    /**
     * [query description]
     * @param  boolean $mode [description]
     * @return [type]        [description]
     */
    public static function query($mode = true)
    {
        return new Query(self::getActuator(), $mode);
    }

    /**
     * [all description]
     * @return [type] [description]
     */
    public static function all()
    {
        $actuator = self::getActuator();
        $table    = $actuator->getTable();

        return new Storage(
            $actuator->fetch("SELECT * FROM {$table}"),
            $actuator
        );
    }

    /**
     * [create description]
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
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

    /**
     * [insert description]
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
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

    /**
     * [count description]
     * @param  [type]  $column   [description]
     * @param  boolean $distinct [description]
     * @return [type]            [description]
     */
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
                ->fetchAll(\PDO::FETCH_ASSOC)[0]['total']
        );
    }

    /**
     * [destroy description]
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
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

    /**
     * [clean description]
     * @return [type] [description]
     */
    public static function clean()
    {
        $actuator = self::getActuator();
        $actuator->fetch('TRUNCATE TABLE ' . $actuator->getTable());

        return true;
    }

    /**
     * [transaction description]
     * @param  Closure $closure [description]
     * @param  [type]  $option  [description]
     * @return [type]           [description]
     */
    public static function transaction(Closure $closure, $option = null)
    {
        return self::getActuator()->transaction($closure, $option);
    }
}
