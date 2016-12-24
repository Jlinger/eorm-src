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
use Eorm\Exceptions\EormException;
use Eorm\Library\Argument;
use Eorm\Library\Helper;
use Eorm\Library\Query;
use Eorm\Library\Where;
use Exception;
use PDO;
use Throwable;

class Eorm
{
    protected static $table      = null;
    protected static $primaryKey = 'id';
    protected static $server     = 'default';

    public static function getTable()
    {
        if (is_null(static::$table)) {
            $splitClassNames = explode('\\', static::class);
            static::$table   = strtolower(end($splitClassNames));
        }

        return static::$table;
    }

    public static function getPrimaryKey()
    {
        return static::$primaryKey;
    }

    public static function getServer()
    {
        return static::$server;
    }

    public static function where($target, $value = null, $option = true, $mode = true)
    {
        if (is_bool($target)) {
            $where = new Where($target);
        } elseif ($target instanceof Closure) {
            $where = new Where(is_bool($value) ? $value : true);
            $target($where);
        } else {
            $where = (new Where($mode))->compare($target, $value, $option);
        }

        return new Query(
            $where,
            static::getTable(),
            static::getPrimaryKey(),
            static::getServer()
        );
    }

    public static function find($ids)
    {
        if (is_array($ids)) {
            $limit = count($ids);
            if ($limit === 1) {
                $ids = reset($ids);
            }
        } else {
            $limit = 1;
        }

        return static::where(static::getPrimaryKey(), $ids)->limit($limit)->get();
    }

    public static function query()
    {
        return static::where(true);
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function create(array $data)
    {
        if (empty($data)) {
            throw new EormException(
                'The database table cannot be inserted into the empty data.'
            );
        }

        $field = Helper::mergeField(array_keys($data));
        $table = Helper::standardise(static::getTable());

        list($argument, $rows, $columns) = Helper::makeInsertArray(array_values($data));

        $values = Helper::fill($rows, Helper::fill($columns), false);

        Server::execute(
            static::getServer(),
            "INSERT INTO {$table} ({$field}) VALUES {$values}",
            $argument
        );

        return static::find(
            Helper::range(Server::insertId(static::getServer()), $rows)
        );
    }

    public static function count()
    {
        $field = Helper::standardise(static::getPrimaryKey());
        $table = Helper::standardise(static::getTable());

        return (int) Server::execute(
            static::getServer(),
            "SELECT COUNT({$field}) AS `total` FROM {$table}"
        )->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
    }

    public static function destroy($ids)
    {
        $table    = Helper::standardise(static::getTable());
        $argument = (new Argument())->push($ids);
        $length   = $argument->count();
        $where    = Helper::makeWhereWithPrimaryKey(static::getPrimaryKey(), $length);

        return Server::execute(
            static::getServer(),
            "DELETE FROM {$table} WHERE {$where} LIMIT {$length}",
            $argument
        )->rowCount();
    }

    public static function clean()
    {
        Server::execute(
            static::getServer(),
            'TRUNCATE TABLE ' . Helper::standardise(static::getTable())
        );
    }

    public static function transaction(Closure $closure, $option = null)
    {
        if (Server::beginTransaction(static::getServer())) {
            try {
                $result = $closure($option);
            } catch (Exception $e) {
                Server::rollBack(static::getServer());
                throw new EormException($e->getMessage());
            } catch (Throwable $e) {
                Server::rollBack(static::getServer());
                throw new EormException($e->getMessage());
            }

            if (!Server::commit(static::getServer())) {
                Server::rollBack(static::getServer());
                throw new EormException('Commit transaction failed.');
            }

            return $result;
        }

        throw new EormException('Failed to start transaction.');
    }
}
