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
namespace Eorm\Exceptions;

use Eorm\Contracts\Exceptions\StatementExceptionInterface;

/**
 * SQL statement execution exception class.
 */
class StatementException extends EormException implements StatementExceptionInterface
{
    /**
     * The exception SQL statement.
     *
     * @var string
     */
    protected $statement;

    /**
     * The exception database server name.
     *
     * @var string
     */
    protected $server;

    /**
     * The exception database table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The exception bound SQL statement parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Construct the statement exception.
     *
     * @param string   $message     The exception message.
     * @param integer  $code        The exception code.
     * @param string   $statement   The exception SQL statement.
     * @param string   $server      The exception database server name.
     * @param string   $table       The exception database table name.
     * @param array    $parameters  The exception bound SQL statement parameters.
     */
    public function __construct($message, $code, $statement, $server, $table, array $parameters = [])
    {
        $this->statement  = $statement;
        $this->server     = $server;
        $this->table      = $table;
        $this->parameters = $parameters;

        parent::__construct($message, $code);
    }

    /**
     * Gets the Exception SQL statement.
     *
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * Gets the Exception database server name.
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets the Exception database table name.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Gets the Exception bound SQL statement parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
