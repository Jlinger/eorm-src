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

use Eorm\Exceptions\EormException;
use Eorm\Library\Argument;
use Exception;
use PDO;
use Throwable;

class Server
{
    protected static $servers = [];

    public static function bind(PDO $pdo, $name = 'default')
    {
        static::$servers[$name] = $pdo;
        return true;
    }

    public static function execute($name, $sql, Argument $argument = null)
    {
        if (!isset(static::$servers[$name])) {
            throw new EormException(
                "Server connection does not exist, with name '{$name}'."
            );
        }

        try {
            $stmt = static::$servers[$name]->prepare($sql);
        } catch (Exception $e) {
            throw new EormException('Execution error: ' . $e->getMessage());
        } catch (Throwable $e) {
            throw new EormException('Execution error: ' . $e->getMessage());
        }

        if (!$stmt) {
            throw new EormException(
                'Execution error: ' . implode(':', static::$servers[$name]->errorInfo())
            );
        }

        if (!$stmt->execute($argument ? $argument->toArray() : [])) {
            throw new EormException(
                'Execution error: ' . implode(':', $stmt->errorInfo())
            );
        }

        return $stmt;
    }

    public static function insertId($name)
    {
        $id = '';

        if (isset(static::$servers[$name])) {
            $id = static::$servers[$name]->lastInsertId();
        }

        return $id;
    }

    public static function beginTransaction($name)
    {
        if (!isset(static::$servers[$name])) {
            return false;
        }

        $pdo = static::$servers[$name];

        if ($pdo->inTransaction()) {
            return true;
        } else {
            return $pdo->beginTransaction();
        }
    }

    public static function commit($name)
    {
        if (!isset(static::$servers[$name])) {
            return false;
        }

        $pdo = static::$servers[$name];

        if ($pdo->inTransaction()) {
            if ($pdo->commit()) {
                return true;
            } else {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                return false;
            }
        } else {
            return true;
        }
    }

    public static function rollBack($name)
    {
        if (!isset(static::$servers[$name])) {
            return false;
        }

        $pdo = static::$servers[$name];

        if ($pdo->inTransaction()) {
            if ($pdo->rollBack()) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
