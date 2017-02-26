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
use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 * The Eorm kernel class.
 */
class Kernel
{
    /**
     * The Eorm event manager instanse.
     *
     * @var Eorm\Foundation\Event
     */
    private static $event = null;

    /**
     * The database server connections.
     *
     * @var array
     */
    private static $connections = [];

    /**
     * Model actuator instanses.
     *
     * @var array
     */
    private static $actuators = [];

    /**
     * Bind a database server connection.
     *
     * @param  string   $name        The database server name.
     * @param  Closure  $connection  The database server connection closure.
     * @return boolean
     */
    public static function bind($name, Closure $connection)
    {
        self::$connections[$name] = $connection;

        return true;
    }

    /**
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return Eorm\Foundation\Actuator
     */
    public static function actuator($abstract)
    {
        if (!isset(self::$actuators[$abstract])) {
            $model = new $abstract();
            $conf  = call_user_func(
                Closure::bind(
                    function () {
                        return [
                            $this->table,
                            $this->primaryKey,
                            $this->server,
                        ];
                    },
                    $model,
                    $model
                )
            );

            $table      = self::parseTable($conf[0], $abstract);
            $primaryKey = self::parsePrimaryKey($conf[1], $abstract);
            $server     = self::parseServer($conf[2], $abstract);

            if (!isset(self::$connections[$server])) {
                throw new EormException(
                    "Model '{$abstract}' connection '{$server}' does not exist.",
                    self::ERROR_CONF
                );
            }

            self::$actuators[$abstract] = new Actuator(
                $table,
                $primaryKey,
                $server,
                self::$connections[$server]
            );
        }

        return self::$actuators[$abstract];
    }

    /**
     * Gets Eorm event instanse.
     *
     * @return Eorm\Foundation\Event
     */
    public static function event()
    {
        if (is_null(self::$event)) {
            self::$event = new Event();
        }

        return self::$event;
    }

    /**
     * Parse table name of model.
     *
     * @param  string|null  $table     Eorm model table name.
     * @param  string       $abstract  Eorm model class fully qualified name.
     * @return string
     */
    private static function parseTable($table, $abstract)
    {
        if (is_string($table)) {
            return $table;
        } elseif (is_null($table)) {
            return strtolower(array_slice(explode('\\', $abstract), -1, 1, false)[0]);
        } else {
            throw new EormException(
                "Model '{$abstract}' table name must be a string or null.",
                Eorm::ERROR_CONF
            );
        }
    }

    /**
     * Parse primary key of model.
     *
     * @param  string|null  $primaryKey  Eorm model primary key.
     * @param  string       $abstract    Eorm model class fully qualified name.
     * @return string
     */
    private static function parsePrimaryKey($primaryKey, $abstract)
    {
        if (is_string($primaryKey)) {
            return $primaryKey;
        } elseif (is_null($primaryKey)) {
            return 'id';
        } else {
            throw new EormException(
                "Model '{$abstract}' primary key name must be a string or null.",
                Eorm::ERROR_CONF
            );
        }
    }

    /**
     * Parse server name of model.
     *
     * @param  string|null  $server    Eorm model server name.
     * @param  string       $abstract  Eorm model class fully qualified name.
     * @return string
     */
    private static function parseServer($server, $abstract)
    {
        if (is_string($server)) {
            return $server;
        } elseif (is_null($server)) {
            return 'default';
        } else {
            throw new EormException(
                "Model '{$abstract}' server name must be a string or null.",
                Eorm::ERROR_CONF
            );
        }
    }
}
