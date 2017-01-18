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

use Closure;
use Eorm\Exceptions\EormException;
use Eorm\Server;
use PDO;

/**
 * Edoger ORM SQL Statement Actuator Class.
 */
final class Actuator extends Server
{
    /**
     * The MySQL database server connection name.
     *
     * @var string
     */
    private $server;

    /**
     * The table name.
     *
     * @var string
     */
    private $table;

    /**
     * The table primary key name.
     *
     * @var string
     */
    private $primaryKey;

    /**
     * The MySQL database server connection.
     *
     * @var PDO
     */
    private $connection = null;

    /**
     * Initialization actuator instanse.
     *
     * @param string  $abstract  Model class name.
     */
    public function __construct($abstract)
    {
        $instanse = new $abstract();

        list($server, $table, $primaryKey) = call_user_func(
            Closure::bind(function ($abstract) {
                if (is_null($this->table)) {
                    $temp        = explode('\\', $abstract);
                    $this->table = strtolower(end($temp));
                }

                return [$this->server, $this->table, $this->primaryKey];
            }, $instanse, $instanse),
            $abstract
        );

        $this->server     = $server;
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Get the table primary key name.
     *
     * @return string
     */
    public function getPrimaryKey($format = true)
    {
        if ($format) {
            return Helper::format($this->primaryKey);
        } else {
            return $this->primaryKey;
        }
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    public function getTable($format = true)
    {
        if ($format) {
            return Helper::format($this->table);
        } else {
            return $this->table;
        }
    }

    /**
     * Execute input SQL statement.
     *
     * @param  string         $sql       The SQL statement.
     * @param  Argument|null  $argument  The execution parameter manager.
     * @return PDOStatement
     */
    public function fetch($sql, Argument $argument = null)
    {
        if ($argument) {
            return $this->execute($sql, $argument->toArray());
        } else {
            return $this->execute($sql, []);
        }
    }

    /**
     * Gets the value of the primary key for the last insert row.
     *
     * @return string
     */
    public function lastId()
    {
        return $this->connection()->lastInsertId();
    }

    /**
     * Start the transaction and execute an action closure.
     *
     * @param  Closure  $action  The action closure.
     * @param  mixed    $option  The closure parameter.
     * @return mixed
     */
    public function transaction(Closure $action, $option = null)
    {
        if ($this->beginTransaction()) {
            try {
                $result = $action($option);
            } catch (Exception $e) {
                $this->rollBack();
                throw new EormException($e->getMessage());
            } catch (Throwable $e) {
                $this->rollBack();
                throw new EormException($e->getMessage());
            }

            if (!$this->commit()) {
                throw new EormException('Commit transaction failed.');
            }

            return $result;
        } else {
            throw new EormException('Failed to begin transaction.');
        }
    }

    /**
     * Get MySQL database server connection.
     *
     * @return PDO
     */
    private function connection()
    {
        if (is_null($this->connection)) {
            $this->connection = $this->getConnection($this->server);
        }

        return $this->connection;
    }

    /**
     * Create and return a PDO statement object.
     *
     * @param  string  $sql  The SQL statement.
     * @return PDOStatement
     */
    private function prepare($sql)
    {
        try {
            $statement = $this->connection()->prepare($sql);
        } catch (\Exception $e) {
            throw new EormException("Create PDO statement object error: {$e->getMessage()}.");
        } catch (\Throwable $e) {
            throw new EormException("Create PDO statement object error: {$e->getMessage()}.");
        }

        if (!$statement) {
            $message = $this->connection()->errorInfo()[2];
            throw new EormException("Create PDO statement object error: {$message}.");
        }

        return $statement;
    }

    /**
     * Execute a SQL statement and return a PDO statement object.
     *
     * @param  string  $sql        The SQL statement.
     * @param  array   $parameter  The execution parameter array.
     * @return PDOStatement
     */
    private function execute($sql, array $parameter)
    {
        $statement = $this->prepare($sql);

        if (!$statement->execute($parameter)) {
            $message = $statement->errorInfo()[2];
            throw new EormException("Execute SQL statement error: {$message}.");
        }

        return $statement;
    }

    /**
     * Begin a transaction.
     *
     * @return boolean
     */
    private static function beginTransaction()
    {
        if ($this->connection()->inTransaction()) {
            return true;
        } else {
            return $this->connection()->beginTransaction();
        }
    }

    /**
     * Commit current transaction.
     *
     * @return boolean
     */
    private static function commit()
    {
        if ($this->connection()->inTransaction()) {
            if ($this->connection()->commit()) {
                return true;
            } else {
                $this->rollBack();
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Roll back current transaction.
     *
     * @return boolean
     */
    private static function rollBack()
    {
        if ($this->connection()->inTransaction()) {
            return $this->connection()->rollBack();
        } else {
            return false;
        }
    }
}
