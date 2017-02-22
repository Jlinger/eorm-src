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
use Eorm\Contracts\EormInterface;
use Eorm\Exceptions\ArgumentException;
use Eorm\Foundation\Actuator;
use PDO;

/**
 * Edoger ORM class.
 */
class Eorm implements EormInterface
{

    /**
     * Enabled state of the Eorm event system.
     *
     * @var boolean
     */
    private static $eventState = false;

    /**
     * Gets Eorm version string.
     *
     * @return string
     */
    public static function version()
    {
        return self::VERSION;
    }

    /**
     * Add a database server connection.
     * If you use a closure as the database server, then the closure must return a PDO object.
     * Duplicate connections are covered when added.
     *
     * @param  PDO|Closure  $connection  Connected PDO connection object or a Closure.
     * @param  string       $name        The database server connection name.
     * @return void
     */
    public static function add($connection, $name = 'default')
    {
        if ($connection instanceof Closure) {
            Actuator::addConnection($connection, $name);
        } elseif ($connection instanceof PDO) {
            Actuator::addConnection(function () use ($connection) {
                return $connection;
            }, $name);
        } else {
            throw new ArgumentException(
                "The connection must be a PDO object or a Closure.",
                self::ERROR_ARGUMENT
            );
        }
    }

    /**
     * Sets/Gets the enabled state of the Eorm event system.
     * Gets the current event enabled state without passing any arguments or passing NULL.
     *
     * @param  boolean|null  $state  Enabled state of the Eorm event system.
     * @return boolean
     */
    public static function event($state = null)
    {
        if (is_bool($state)) {
            self::$eventState = $state;
        }

        return self::$eventState;
    }
}
