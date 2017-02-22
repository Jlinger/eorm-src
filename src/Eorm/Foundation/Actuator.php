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
namespace Eorm\Foundation;

use Closure;
use Eorm\Contracts\ActuatorInterface;
use Eorm\Contracts\ModelInterface;
use Eorm\Eorm;
use Eorm\Event;
use Eorm\Exceptions\ConfigurationException;
use Eorm\Exceptions\ConnectException;
use Eorm\Exceptions\StatementException;
use Exception;
use PDO;
use PDOStatement;
use Throwable;

/**
 * Eorm SQL statement actuator class.
 */
class Actuator implements ActuatorInterface
{
    /**
     * Model actuator instanses.
     *
     * @var array
     */
    private static $actuatorInstanses = [];

    /**
     * The actuator associated database table name.
     *
     * @var string
     */
    private $table;

    /**
     * The actuator associated database table primary key name.
     *
     * @var string
     */
    private $primaryKey;

    /**
     * The actuator associated database server name.
     *
     * @var string
     */
    private $server;

    /**
     * The actuator associated database server connection.
     *
     * @var PDO|Closure
     */
    private $connection = null;

    /**
     * The actuator associated database connection is already connected.
     *
     * @var boolean
     */
    private $connected = false;

    /**
     * The fetch mode for all PDOstatement object.
     *
     * @var integer
     */
    private $fetchModel = PDO::FETCH_ASSOC;

    /**
     * Initialize this actuator instanse.
     *
     * @param ModelInterface  $abstract    Eorm model class fully qualified name.
     * @param PDO|Closure     $connection  Current actuator associated database server connection.
     */
    public function __construct(ModelInterface $model, $connection)
    {
        $this->table      = $model->getTable();
        $this->primaryKey = $model->getPrimaryKey();
        $this->server     = $model->getServer();
        $this->connection = $connection;

        if ($connection instanceof PDO) {
            $this->connected = true;
        }
    }

    /**
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return ActuatorInterface
     */
    public static function getActuator($abstract)
    {
        if (!isset(self::$actuatorInstanses[$abstract])) {
            $model  = new $abstract();
            $server = $model->getServer();

            if (!isset(self::$serverConnections[$server])) {
                throw new ConfigurationException(
                    "The model associated database connection '{$server}' does not exist.",
                    self::ERROR_CONFIGURATION,
                    $abstract,
                    'server'
                );
            }

            self::$actuatorInstanses[$abstract] = new Actuator(
                $model,
                self::$serverConnections[$server]
            );
        }

        return self::$actuatorInstanses[$abstract];
    }

    /**
     * Gets the actuator associated database table name.
     *
     * @param  boolean  $format  Returns the formatted database table name ? (yes)
     * @return string
     */
    public function table($format = true)
    {
        if ($format) {
            return Helper::format($this->table);
        } else {
            return $this->table;
        }
    }

    /**
     * Gets the actuator associated database table primary key name.
     *
     * @param  boolean  $format  Returns the formatted database table primary key name ? (yes)
     * @return string
     */
    public function primaryKey($format = true)
    {
        if ($format) {
            return Helper::format($this->primaryKey);
        } else {
            return $this->primaryKey;
        }
    }

    /**
     * Gets the actuator associated database server name.
     *
     * @return string
     */
    public function server()
    {
        return $this->server;
    }

    /**
     * Gets the actuator associated database server connection.
     *
     * @return PDO
     */
    public function connection()
    {
        if ($this->connected) {
            return $this->connection;
        }

        try {
            $connection = call_user_func($this->connection, $this->server());
        } catch (Exception $e) {
            throw new ConnectException($e->getMessage(), Eorm::ERROR_CONNECT, $this->server());
        } catch (Throwable $e) {
            throw new ConnectException($e->getMessage(), Eorm::ERROR_CONNECT, $this->server());
        }

        if ($connection instanceof PDO) {
            $this->connection = $connection;
            $this->connected  = true;
        } else {
            throw new ConnectException(
                "Connection closure must return a PDO connection object.",
                Eorm::ERROR_CONNECT,
                $this->server()
            );
        }

        return $connection;
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     * This method is used to execute a non query SQL statement without parameters.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement  The SQL statement to prepare and execute.
     * @param  integer  $type       The SQL statement type.
     * @return integer
     */
    public function execute($statement, $type)
    {
        if (Eorm::event()) {
            if (Event::exists($type)) {
                Event::trigger(new EventBody($type, [
                    'statement'  => $statement,
                    'parameters' => [],
                    'server'     => $this->server(),
                    'table'      => $this->table(false),
                    'type'       => $type,
                ]));
            }

            if (Event::exists('execute')) {
                Event::trigger(new EventBody('execute', [
                    'statement'  => $statement,
                    'parameters' => $parameters,
                    'server'     => $this->server(),
                    'table'      => $this->table(false),
                    'type'       => $type,
                ]));
            }
        }

        $rows = $this->connection()->query($statement);
        if (!is_int($rows)) {
            if ($this->hasError()) {
                throw new StatementException(
                    $this->getErrorMessage(),
                    Eorm::ERROR_STATEMENT,
                    $statement,
                    $this->server(),
                    $this->table(false)
                );
            } else {
                $rows = intval($rows);
            }
        }

        return $rows;
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement   The SQL statement to prepare and execute.
     * @param  integer  $type        The SQL statement type.
     * @param  array    $parameters  Binding parameters of SQL statement.
     * @return PDOStatement
     */
    public function query($statement, $type, array $parameters = [])
    {
        if (Eorm::event()) {
            if (Event::exists($type)) {
                Event::trigger(new EventBody($type, [
                    'statement'  => $statement,
                    'parameters' => $parameters,
                    'server'     => $this->server(),
                    'table'      => $this->table(false),
                    'type'       => $type,
                ]));
            }

            if (Event::exists('execute')) {
                Event::trigger(new EventBody('execute', [
                    'statement'  => $statement,
                    'parameters' => $parameters,
                    'server'     => $this->server(),
                    'table'      => $this->table(false),
                    'type'       => $type,
                ]));
            }
        }

        if (empty($parameters)) {
            $object = $this->connection()->query($statement);
            if (!$object) {
                throw new StatementException(
                    $this->getErrorMessage(),
                    Eorm::ERROR_STATEMENT,
                    $statement,
                    $this->server(),
                    $this->table(false)
                );
            }
        } else {
            try {
                $object = $this->connection()->prepare($statement);
            } catch (Exception $e) {
                throw new StatementException(
                    $e->getMessage(),
                    Eorm::ERROR_STATEMENT,
                    $statement,
                    $this->server(),
                    $this->table(false),
                    $parameters
                );
            }
            if (!$object) {
                throw new StatementException(
                    $this->getErrorMessage(),
                    Eorm::ERROR_STATEMENT,
                    $statement,
                    $this->server(),
                    $this->table(false),
                    $parameters
                );
            }
            if (!$object->execute($parameters)) {
                throw new StatementException(
                    $this->getErrorMessage($object),
                    Eorm::ERROR_STATEMENT,
                    $statement,
                    $this->server(),
                    $this->table(false),
                    $parameters
                );
            }
        }

        $object->setFetchMode($this->fetchModel);

        return $object;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param  string|null  $name  Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function getLastInsertId($name = null)
    {
        return $this->connection()->lastInsertId();
    }

    /**
     * Checks if inside a transaction.
     *
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->connection()->inTransaction();
    }

    /**
     * Initiates a transaction.
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        if ($this->inTransaction()) {
            return true;
        } else {
            return $this->connection()->beginTransaction();
        }
    }

    /**
     * Commits a transaction.
     *
     * @return boolean
     */
    public function commit()
    {
        if ($this->inTransaction()) {
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
     * Rolls back a transaction.
     *
     * @return boolean
     */
    public function rollBack()
    {
        if ($this->inTransaction()) {
            return $this->connection()->rollBack();
        } else {
            return false;
        }
    }

    /**
     * Gets the error message generated by the last database operation.
     * If a PDOStatement object is given, the message will be obtained from the PDOStatement object,
     * or it will get the message from the database connection object.
     *
     * @param  PDOStatement|null  $statement  The PDO statement instanse.
     * @return string
     */
    protected function getErrorMessage(PDOStatement $statement = null)
    {
        if ($statement) {
            $information = $statement->errorInfo();
        } else {
            $information = $this->connection()->errorInfo();
        }

        if ($information && isset($information[2])) {
            return $information[2];
        } else {
            return 'Unknown error.';
        }
    }

    /**
     * Check whether the last database operation has an error.
     * If a PDOStatement object is given, the PDOStatement object is checked,
     * otherwise the database connection object will be checked.
     *
     * @param  PDOStatement|null  $statement  The PDO statement instanse.
     * @return boolean
     */
    protected function hasError(PDOStatement $statement = null)
    {
        if ($statement) {
            return $statement->errorCode() !== PDO::ERR_NONE;
        } else {
            return $this->connection()->errorCode() !== PDO::ERR_NONE;
        }
    }
}
