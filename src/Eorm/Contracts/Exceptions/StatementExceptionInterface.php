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
namespace Eorm\Contracts\Exceptions;

/**
 * SQL statement execution exception class interface.
 */
interface StatementExceptionInterface
{
    /**
     * Gets the Exception SQL statement.
     *
     * @return string
     */
    public function getStatement();

    /**
     * Gets the Exception database server name.
     *
     * @return string
     */
    public function getServer();

    /**
     * Gets the Exception database table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Gets the Exception bound SQL statement parameters.
     *
     * @return array
     */
    public function getParameters();
}
