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

use Eorm\Library\Query;
use Eorm\Library\Storage;
use Eorm\Library\Where;

/**
 * Eorm model base class.
 * All model classes should extends this class.
 */
class Model
{
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
     * Get the current model using database table name.
     *
     * @return string
     */
    public static function table()
    {
        return Kernel::executor(static::class)->table();
    }

    /**
     * Get the current model using database table primary key name.
     *
     * @return string
     */
    public static function primaryKey()
    {
        return Kernel::executor(static::class)->primaryKey();
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
        return self::query($mode)->where($target, $value, $option);
    }

    /**
     * Query data by primary key.
     *
     * @param  integer|array  $ids  The primary keys.
     * @return Storage
     */
    public static function find($ids, $fields = [])
    {
        return self::where(self::primaryKey(), $ids)->get($fields);
    }

    /**
     * Create a Query instanse.
     *
     * @param  boolean  $mode  The where mode.
     * @return Query
     */
    public static function query()
    {
        return new Query(Kernel::executor(static::class));
    }

    /**
     * Query all data.
     *
     * @return Storage
     */
    public static function all($fields = [])
    {
        return self::query($mode)->get($fields);
    }

    /**
     * Insert some rows and return storage.
     *
     * @param  array  $columns  The columns data.
     * @return Storage
     */
    public static function create(array $columns)
    {
        return self::query()->create($columns);
    }

    /**
     * Insert some rows and return primary keys.
     *
     * @param  array  $columns  The columns data.
     * @return array|integer
     */
    public static function insert(array $columns)
    {
        return self::query()->insert($columns);
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
        return self::query()->count($column, $distinct);
    }

    /**
     * Delete data by primary key.
     *
     * @param  integer|array  $ids  The primary keys.
     * @return integer
     */
    public static function destroy($ids)
    {
        return self::query()->destroy($ids);
    }

    /**
     * Clean all data.
     *
     * @return boolean
     */
    public static function clean()
    {
        return self::query()->clean();
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
        return Kernel::executor(static::class)->transaction($closure, $option);
    }
}
