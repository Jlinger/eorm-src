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

/**
 *
 */
class Kernel extends SchedulerInterface
{
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
            $model  = new $abstract();
            $server = $model->getServer();

            if (!isset(self::$connections[$server])) {
                throw new ConfigurationException(
                    "The model associated database connection '{$server}' does not exist.",
                    self::ERROR_CONFIGURATION,
                    $abstract,
                    'server'
                );
            }

            self::$actuators[$abstract] = new Actuator($model, self::$connections[$server]);
        }

        return self::$actuators[$abstract];
    }
}
