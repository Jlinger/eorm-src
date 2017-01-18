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
use Eorm\Exceptions\EormException;
use PDO;

/**
 * Edoger ORM Database Server Connection Manager Class.
 */
class Server
{
    /**
     * The MySQL database server connections.
     *
     * @var array
     */
    private static $connections = [];

    private static $hooks = [];

    /**
     * Add a connection to the connection stack.
     * If you use a closure to create a connection, make sure that the closure must return a PDO object.
     *
     * @param  PDO|Closure  $connection  Connected PDO connection object or a Closure.
     * @param  string       $name        The MySQL database server connection name.
     * @return void
     */
    public static function add($connection, $name = 'default')
    {
        if ($connection instanceof PDO || $connection instanceof Closure) {
            self::$connections[$name] = $connection;
        } else {
            throw new EormException("The connection must be a PDO object or a Closure.");
        }
    }

    /**
     * [hook description]
     * @param  Closure $action    [description]
     * @param  [type]  $parameter [description]
     * @return [type]             [description]
     */
    public static function hook(Closure $action, $parameter = null)
    {
        self::$hooks[] = [$action, $parameter];

        return true;
    }

    /**
     * Get MySQL database server connection by name.
     *
     * @param  string  $name  The MySQL database server connection name.
     * @return PDO
     */
    protected function getConnection($name)
    {
        if (isset(self::$connections[$name])) {
            if (self::$connections[$name] instanceof PDO) {
                return self::$connections[$name];
            } else {
                $connection = call_user_func(self::$connections[$name]);
                if ($connection instanceof PDO) {
                    self::$connections[$name] = $connection;
                    return $connection;
                } else {
                    throw new EormException("The connection Closure must return a PDO object.");
                }
            }
        } else {
            throw new EormException("The connection '{$name}' does not exist.");
        }
    }

    /**
     * [callHooks description]
     * @param  [type] $statement [description]
     * @param  array  $arguments [description]
     * @param  [type] $server    [description]
     * @param  [type] $table     [description]
     * @return [type]            [description]
     */
    protected function callHooks($statement, array $arguments, $server, $table)
    {
        if (!empty(self::$hooks)) {
            foreach (self::$hooks as $hook) {
                call_user_func($hook[0], $statement, $arguments, $server, $table, $hook[1]);
            }
        }

        return $this;
    }
}
