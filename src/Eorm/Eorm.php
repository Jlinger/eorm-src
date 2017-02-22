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
use Eorm\Foundation\Event;
use Eorm\Foundation\Kernel;
use PDO;

/**
 * Edoger ORM class.
 */
class Eorm implements EormInterface
{
    /**
     * Eorm version string constant.
     */
    const VERSION = '1.0.0-dev';

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
     * Bind a database server connection.
     * If you use a closure as the database server, then the closure must return a PDO object.
     * Duplicate connections are covered when added.
     *
     * @param  PDO|Closure  $connection  Connected PDO connection object or a Closure.
     * @param  string       $name        The database server connection name.
     * @return void
     */
    public static function grip($connection, $name = 'default')
    {
        if ($connection instanceof Closure) {
            Kernel::bind($name, $connection);
        } elseif ($connection instanceof PDO) {
            Kernel::bind($name, function () use ($connection) {
                return $connection;
            });
        } else {
            throw new ArgumentException(
                "The connection must be a PDO object or a Closure.",
                self::ERROR_ARGUMENT
            );
        }
    }

    /**
     * Sets the enabled state of the Eorm event system.
     *
     * @param  boolean  $state  Enabled state of the Eorm event system.
     * @return boolean
     */
    public static function event($state)
    {
        if ($state) {
            Event::open();
        } else {
            Event::close();
        }

        return Event::state();
    }

    /**
     * Register a Eorm event handler.
     *
     * @param  string                 $name     The Eorm event name.
     * @param  EventHandlerInterface  $handler  The Eorm event handler.
     * @return integer
     */
    public static function on($name, EventHandlerInterface $handler)
    {
        return Event::on(strtolower($name), $handler);
    }

    /**
     * Delete a Eorm event handler.
     *
     * @param  string   $name   The Eorm event name.
     * @param  boolean  $clean  Delete all event handlers ? (no)
     * @return EventHandlerInterface|array|null
     */
    public static function off($name, $clean = false)
    {
        if ($clean) {
            return Event::clean(strtolower($name));
        } else {
            return Event::off(strtolower($name));
        }
    }
}
