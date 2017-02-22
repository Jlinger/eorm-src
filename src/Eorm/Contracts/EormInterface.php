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
 * Edoger ORM class interface.
 */
interface EormInterface
{

    const ERROR_RUNTIME       = 1001;
    const ERROR_CONNECT       = 1002;
    const ERROR_CONFIGURATION = 1003;
    const ERROR_ARGUMENT      = 1004;
    const ERROR_STATEMENT     = 1005;
    const ERROR_TRANSACTION   = 1006;
    const ERROR_EVENT         = 1007;

    const SQL_SELECT  = 6001;
    const SQL_UPDATE  = 6002;
    const SQL_INSERT  = 6003;
    const SQL_DELETE  = 6004;
    const SQL_REPLACE = 6005;
    const SQL_CLEAN   = 6006;
    const SQL_COUNT   = 6007;
    const SQL_EXISTS  = 6008;

    /**
     * Gets Eorm version string.
     *
     * @return string
     */
    public static function version();

    /**
     * Add a database server connection.
     * If you use a closure as the database server, then the closure must return a PDO object.
     * Duplicate connections are covered when added.
     *
     * @param  PDO|Closure  $connection  Connected PDO connection object or a Closure.
     * @param  string       $name        The database server connection name.
     * @return void
     */
    public static function add($connection, $name = 'default');

    /**
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return ActuatorInterface
     */
    public static function getActuator($abstract);

    /**
     * Sets/Gets the enabled state of the Eorm event system.
     * Gets the current event enabled state without passing any arguments or passing NULL.
     *
     * @param  boolean|null  $state  Enabled state of the Eorm event system.
     * @return boolean
     */
    public static function event($state = null);
}
