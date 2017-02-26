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

use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 * Eorm SQL statement parameter manager class.
 */
class Parameter
{
    /**
     * The binding parameters of SQL statement.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Initialize this parameter manager instanse.
     *
     * @param array|string|number|boolean|null  $params  The binding parameters of SQL statement.
     */
    public function __construct($params = [])
    {
        if (is_array($params)) {
            if (!empty($params)) {
                $this->pushMany($params);
            }
        } else {
            $this->push($params);
        }
    }

    /**
     * Push a binding parameter of the SQL statement.
     * The binding parameter of the SQL statement must be a scalar.
     *
     * @param  string|number|boolean|null  $param  The binding parameter of SQL statement.
     * @return Eorm\Foundation\Parameter
     */
    public function push($param)
    {
        if (is_string($parameter) || is_numeric($parameter)) {
            $this->parameters[] = $parameter;
        } elseif (is_bool($value)) {
            $this->parameters[] = $parameter ? 1 : 0;
        } elseif (is_null($value)) {
            $this->parameters[] = '';
        } else {
            throw new EormException(
                "The binding parameter of the SQL statement must be a scalar.",
                Eorm::ERROR_ARGUMENT
            );
        }

        return $this;
    }

    /**
     * Push multiple binding parameters of the SQL statement.
     *
     * @param  array  $params  The binding parameters of SQL statement.
     * @return Eorm\Foundation\Parameter
     */
    public function pushMany(array $params)
    {
        foreach ($parameters as $parameter) {
            $this->push($parameter);
        }

        return $this;
    }

    /**
     * Merge parameter.
     *
     * @param  Eorm\Foundation\Parameter  $parameter  The SQL statement parameter.
     * @return Eorm\Foundation\Parameter
     */
    public function merge(Parameter $parameter)
    {
        $this->pushMany($parameter->toArray());

        return $this;
    }

    /**
     * Gets the number of binding parameters that have been added.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * Output all binding parameters.
     *
     * @param  boolean  $clean  Clean all binding parameters ? (no)
     * @return array
     */
    public function toArray($clean = false)
    {
        if ($clean) {
            $parameters = $this->parameters;
            $this->clean();

            return $parameters;
        } else {
            return $this->parameters;
        }
    }

    /**
     * Clean all binding parameters.
     *
     * @return Eorm\Foundation\Parameter
     */
    public function clean()
    {
        $this->parameters = [];

        return $this;
    }
}
