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
 *
 */
class Kernel
{

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
     * @return void
     */
    public static function bind($name, Closure $connection)
    {
        self::$connections[$name] = $connection;
    }

    /**
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return ActuatorInterface
     */
    public static function actuator($abstract)
    {
        if (!isset(self::$actuators[$abstract])) {
            $model = new $abstract();

            list($table, $primaryKey, $server) = call_user_func(Closure::bind(function ($abstract) {
                if (is_string($this->table) && $this->table !== '') {
                    $table = $this->table;
                } elseif (is_null($this->table)) {
                    $split       = explode('\\', $abstract);
                    $this->table = strtolower(end($split));
                    $table       = $this->table;
                } else {
                    throw new EormException(
                        "Model database table name must be a non empty string.",
                        Eorm::ERROR_CONF
                    );
                }

                if (is_string($this->primaryKey) && $this->primaryKey !== '') {
                    $primaryKey = $this->primaryKey;
                } else {
                    throw new EormException(
                        "Model database table primary key name must be a non empty string.",
                        Eorm::ERROR_CONF
                    );
                }

                if (is_string($this->server) && $this->server !== '') {
                    $server = $this->server;
                } else {
                    throw new EormException(
                        "Model database server name must be a non empty string.",
                        Eorm::ERROR_CONF
                    );
                }

                return [$table, $primaryKey, $server];
            }, $model, $model), $abstract);

            if (!isset(self::$connections[$server])) {
                throw new EormException(
                    "Model database connection '{$server}' does not exist.",
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

    public static function event()
    {
        if (is_null(self::$event)) {
            self::$event = new Event();
        }

        return self::$event;
    }
}
