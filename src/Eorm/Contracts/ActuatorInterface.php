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
namespace Eorm\Contracts;

/**
 * Eorm SQL statement actuator class interface.
 */
interface ActuatorInterface
{
    /**
     * Gets the actuator associated database table name.
     *
     * @param  boolean  $format  Returns the formatted database table name ? (yes)
     * @return string
     */
    public function table($format = true);

    /**
     * Gets the actuator associated database table primary key name.
     *
     * @param  boolean  $format  Returns the formatted database table primary key name ? (yes)
     * @return string
     */
    public function primaryKey($format = true);

    /**
     * Gets the actuator associated database server name.
     *
     * @return string
     */
    public function server();

    /**
     * Gets the actuator associated database server connection.
     *
     * @return PDO
     */
    public function connection();

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     * This method is used to execute a query SQL statement without parameters.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement  The SQL statement to prepare and execute.
     * @param  integer  $type       The SQL statement type.
     * @return PDOStatement
     */
    public function query($statement, $type);

    /**
     * Execute an SQL statement and return the number of affected rows.
     * This method is used to execute a non query SQL statement without parameters.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement  The SQL statement to prepare and execute.
     * @param  integer  $type       The SQL statement type.
     * @return integer
     */
    public function execute($statement, $type);

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     * This method is used to execute a query SQL statement with parameters.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement   The SQL statement to prepare and execute.
     * @param  array    $parameters  Binding parameters of SQL statement.
     * @param  integer  $type        The SQL statement type.
     * @return PDOStatement
     */
    public function read($statement, array $parameters, $type);

    /**
     * Execute an SQL statement and return the number of affected rows.
     * This method is used to execute a non query SQL statement with parameters.
     * If the SQL statement fails, an 'StatementException' exception is thrown.
     *
     * @param  string   $statement   The SQL statement to prepare and execute.
     * @param  array    $parameters  Binding parameters of SQL statement.
     * @param  integer  $type        The SQL statement type.
     * @return PDOStatement
     */
    public function write($statement, array $parameters, $type);

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param  string|null  $name  Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function getLastInsertId($name = null);

    /**
     * Initiates a transaction.
     *
     * @return boolean
     */
    public function beginTransaction();

    /**
     * Commits a transaction.
     *
     * @return boolean
     */
    public function commit();

    /**
     * Rolls back a transaction.
     *
     * @return boolean
     */
    public function rollBack();
}
