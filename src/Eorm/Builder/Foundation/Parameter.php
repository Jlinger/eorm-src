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
namespace Eorm\Builder\Foundation;

use Countable;
use Eorm\Contracts\Arrayable;
use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 * The Eorm SQL statement parameter manager class.
 */
class Parameter implements Countable, Arrayable
{
    /**
     * The SQL statement parameters.
     *
     * @var array
     */
    protected $caches = [];

    /**
     * Get all SQL statement parameters.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->caches;
    }

    /**
     * Gets the number of parameters that have been appended.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->caches);
    }

    /**
     * Append a SQL statement parameter.
     *
     * @param  mixed  $param  The SQL statement parameter.
     * @return Parameter
     */
    public function push($param)
    {
        if (is_string($param) || is_numeric($param)) {
            $this->caches[] = $param;
        } elseif (is_array($param)) {
            foreach ($param as $value) {
                $this->push($value);
            }
        } elseif (is_bool($value)) {
            $this->caches[] = $param ? 1 : 0;
        } elseif ($param instanceof Arrayable) {
            foreach ($param->toArray() as $value) {
                $this->push($value);
            }
        } elseif (is_null($value)) {
            $this->caches[] = '';
        } else {
            throw new EormException(
                'The SQL statement argument must be a scalar.',
                Eorm::ERROR_ARGUMENT
            );
        }

        return $this;
    }

    /**
     * Clean all binding parameters.
     *
     * @return Parameter
     */
    public function clean()
    {
        $this->caches = [];

        return $this;
    }
}
