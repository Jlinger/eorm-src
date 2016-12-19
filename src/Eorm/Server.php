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
use Eorm\Library\Helper;
use Exception;
use PDO;
use Throwable;

class Server
{
    protected static $pdo = null;

    public static function bind(PDO $pdo)
    {
        static::$pdo = $pdo;
        return true;
    }

    public static function execute($sql, Argument $argument = null)
    {
        if (!static::$pdo) {
            throw new EormException("No database connection available.");
        }

        Event::triggerExecute($sql, $argument ? $argument->toArray() : []);

        try {
            $statement = static::$pdo->prepare($sql);
            if ($statement && $statement->execute($argument ? $argument->toArray() : [])) {
                return $statement;
            }
        } catch (Exception $e) {
            throw new EormException('Execute SQL error: ' . $e->getMessage());
        } catch (Throwable $e) {
            throw new EormException('Execute SQL error: ' . $e->getMessage());
        }

        $information = $statement ? $statement->errorInfo() : static::$pdo->errorInfo();
        if ($information && isset($information[2])) {
            throw new EormException('Execute SQL error: ' . $information[2]);
        } else {
            throw new EormException('Execute SQL error: Unknown error.');
        }
    }

    public static function id($length = 0)
    {
        return static::$pdo ? Helper::range((int) static::$pdo->lastInsertId(), $length) : 0;
    }

    public static function beginTransaction()
    {
        return static::$pdo && !static::$pdo->inTransaction() && static::$pdo->beginTransaction();
    }

    public static function commit()
    {
        return static::$pdo && static::$pdo->inTransaction() && static::$pdo->commit();
    }

    public static function rollBack()
    {
        return static::$pdo && static::$pdo->inTransaction() && static::$pdo->rollBack();
    }
}
