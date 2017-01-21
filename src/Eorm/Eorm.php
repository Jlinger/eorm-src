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
use Eorm\Exceptions\ConfigurationException;
use Eorm\Exceptions\EormException;
use Eorm\Foundation\Actuator;
use PDO;

/**
 * Edoger ORM class.
 */
class Eorm implements EormInterface
{
    /**
     * Model actuator instanses.
     *
     * @var array
     */
    private static $actuators = [];

    /**
     * The database server connections.
     *
     * @var array
     */
    private static $connections = [];

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
     * Gets Eorm model actuator instanse.
     *
     * @param  string  $abstract  Eorm model class fully qualified name.
     * @return ActuatorInterface
     */
    public static function getActuator($abstract)
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
        if ($connection instanceof PDO || $connection instanceof Closure) {
            self::$connections[$name] = $connection;
        } else {
            throw new EormException("The connection must be a PDO object or a Closure.");
        }
    }
}
