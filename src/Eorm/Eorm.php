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
namespace Eorm;

use Closure;
use Eorm\Foundation\Kernel;
use PDO;

/**
 * Edoger object relational mapping manager class.
 */
class Eorm
{
    /**
     * Eorm version string constant.
     */
    const VERSION = '1.0.0-dev';

    const ERROR_RUNTIME  = 1001;
    const ERROR_CONF     = 1002;
    const ERROR_ARGUMENT = 1003;
    const ERROR_SQL      = 1004;
    const ERROR_EVENT    = 1005;

    /**
     * Get eorm version.
     *
     * @return string
     */
    public static function version()
    {

        return self::VERSION;
    }

    /**
     * Bind a database server connection.
     * If you use a closure as the database server, then the closure must return a PDO object.
     * Duplicate connections are covered when added.
     *
     * @param  PDO|Closure  $connection  Connected PDO connection object or a Closure.
     * @param  string       $name        The database server connection name.
     * @return boolean
     */
    public static function server($connection, $name = 'default')
    {
        if ($connection instanceof Closure) {
            return Kernel::bind($name, $connection);
        } elseif ($connection instanceof PDO) {
            return Kernel::bind($name, function () use ($connection) {
                return $connection;
            });
        } else {
            return false;
        }
    }

    /**
     * Gets Eorm event instanse.
     *
     * @return Eorm\Foundation\Event
     */
    public static function event()
    {

        return Kernel::event();
    }

    /**
     * Register a Eorm event handler.
     *
     * @param  EventHandlerAbstract  $handler  The Eorm event handler.
     * @return boolean
     */
    public static function on(EventHandlerAbstract $handler)
    {

        return Kernel::event()->bind($handler);
    }

    /**
     * Delete all Eorm event handler by event name.
     *
     * @param  string  $name  The Eorm event name.
     * @return boolean
     */
    public static function off($name)
    {

        return Kernel::event()->off(strtolower($name));
    }
}
