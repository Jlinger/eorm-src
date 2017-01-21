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

use Eorm\Contracts\BufferInterface;
use Eorm\Eorm;
use Eorm\Exceptions\ArgumentException;

/**
 * Eorm SQL statement parameter buffer component.
 */
class Buffer implements BufferInterface
{
    /**
     * The binding parameters of SQL statement.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Initialize this buffer instanse.
     *
     * @param array|string|number|boolean|null  $parameters  The binding parameters of SQL statement.
     */
    public function __construct($parameters = [])
    {
        $this->push($parameters);
    }

    /**
     * Push the binding parameters of the SQL statement.
     * The binding parameter of the SQL statement must be a scalar.
     *
     * @param  array|string|number|boolean|null  $parameters  The binding parameters of SQL statement.
     * @return Buffer
     */
    public function push($parameters)
    {
        if (is_array($parameters)) {
            if (empty($parameters)) {
                return $this;
            }
        } else {
            $parameters = [$parameters];
        }

        foreach ($parameters as $parameter) {
            if (is_string($parameter) || is_numeric($parameter)) {
                $this->parameters[] = $parameter;
            } elseif (is_bool($value)) {
                $this->parameters[] = $parameter ? 1 : 0;
            } elseif (is_null($value)) {
                $this->parameters[] = '';
            } else {
                throw new ArgumentException(
                    "The binding parameter of the SQL statement must be a scalar.",
                    Eorm::ERROR_ARGUMENT
                );
            }
        }

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
    public function output($clean = false)
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
     * @return Buffer
     */
    public function clean()
    {
        $this->parameters = [];

        return $this;
    }
}
