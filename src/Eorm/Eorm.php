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

use Eorm\Library\Actuator;
use Eorm\Library\Argument;
use Eorm\Library\Builder;
use Eorm\Library\Helper;
use Eorm\Library\Query;
use Eorm\Library\Storage;
use Eorm\Library\Where;

/**
 * Eorm Model Base Class.
 * All Model Classes Should Extends This Class.
 */
class Eorm
{
    /**
     * Model actuator instanses.
     *
     * @var array
     */
    private static $actuators = [];

    /**
     * Default database table name.
     *
     * @var null
     */
    protected $table = null;

    /**
     * Default database table primary key name.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Default database server name.
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
     * Get database table name.
     *
     * @return string
     */
    public static function getTable()
    {
        return self::getActuator()->getTable(false);
    }

    /**
     * Get database table primary key name.
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
     * @param  string|Closure  $target  The field name or a closure.
     * @param  mixed           $value   The condition value or where mode.
     * @param  boolean|string  $option  The connector.
     * @param  boolean         $mode    The where mode.
     * @return Query
     */
    public static function where($target, $value = null, $option = true, $mode = true)
    {
        return static::query($mode)->where($target, $value, $option);
    }

    /**
     * Query data by primary key.
     *
     * @param  integer|array  $ids  The primary keys.
     * @return Storage
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
     * Create a Query instanse.
     *
     * @param  boolean  $mode  The where mode.
     * @return Query
     */
    public static function query($mode = true)
    {
        return new Query(self::getActuator(), $mode);
    }

    /**
     * Query all data.
     *
     * @return Storage
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
     * Insert some rows and return storage.
     *
     * @param  array  $columns  The columns data.
     * @return Storage
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
     * Insert some rows and return primary keys.
     *
     * @param  array  $columns  The columns data.
     * @return array|integer
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
     * Query total number of rows by column name or primary key(default).
     *
     * @param  string|null  $column    The column name.
     * @param  boolean      $distinct  Eliminate duplicate data ? (no)
     * @return integer
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
     * Delete data by primary key.
     *
     * @param  integer|array  $ids  The primary keys.
     * @return integer
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
     * Clean all data.
     *
     * @return boolean
     */
    public static function clean()
    {
        $actuator = self::getActuator();
        $actuator->fetch('TRUNCATE TABLE ' . $actuator->getTable());

        return true;
    }

    /**
     * Begin a transaction, and performing multiple database operations.
     *
     * @param  Closure  $closure  The action closure.
     * @param  mixed    $option   The action closure other parameter.
     * @return mixed
     */
    public static function transaction(\Closure $closure, $option = null)
    {
        return self::getActuator()->transaction($closure, $option);
    }
}
