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
    /**
     * Eorm version string constant.
     */
    const VERSION = '1.0.0-b1';

    const ERROR_RUNTIME       = 1001;
    const ERROR_CONNECT       = 1002;
    const ERROR_CONFIGURATION = 1003;
    const ERROR_ARGUMENT      = 1004;
    const ERROR_STATEMENT     = 1005;
    const ERROR_TRANSACTION   = 1006;
    const ERROR_EVENT         = 1007;

    /**
     * Gets Eorm version.
     *
     * @return string
     */
    public static function version();

    /**
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return ActuatorInterface
     */
    public static function getActuator($abstract);

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
}
