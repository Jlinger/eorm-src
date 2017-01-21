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
namespace Eorm\Builder;

use Closure;
use Eorm\Eorm;
use Eorm\Exceptions\ArgumentException;
use Eorm\Foundation\Buffer;
use Eorm\Library\Helper;

/**
 * SQL where condition builder class.
 */
class Where
{
    /**
     * Connect mode.
     * If it is TRUE, will use the 'AND' connector, or use the 'OR' connector.
     *
     * @var boolean
     */
    protected $mode;

    /**
     * The parameter buffer component instanse.
     *
     * @var BufferInterface
     */
    protected $buffer;

    /**
     * The connection conditions.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Initialize this condition builder instanse.
     *
     * @param boolean  $mode  The connect mode.
     */
    public function __construct($mode = true)
    {
        $this->mode   = boolval($mode);
        $this->buffer = new Buffer();
    }

    /**
     * Gets/Sets the current connection mode.
     * Gets the current connection mode without passing any arguments or passing NULL.
     *
     * @param  boolean|null  $mode  The connect mode.
     * @return boolean
     */
    public function mode($mode = null)
    {
        if (is_bool($mode)) {
            $this->mode = $mode;
        }

        return $this->mode;
    }

    /**
     * Clear all currently set SQL where conditions.
     *
     * @return Where
     */
    public function clean()
    {
        $this->conditions = [];
        $this->buffer()->clean();

        return $this;
    }

    /**
     * Add a SQL where condition.
     *
     * @param  string                    $field   [description]
     * @param  string|number|array|null  $value   [description]
     * @param  boolean|string            $option  [description]
     * @return Where
     */
    public function condition($field, $value, $option = true)
    {
        $formattedField = Helper::format($field);

        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            $this->buffer()->push($value);
            if (is_bool($option)) {
                if ($option) {
                    $this->push($formattedField . '=?');
                } else {
                    $this->push($formattedField . '!=?');
                }
            } elseif (is_string($option)) {
                if (!in_array($option, ['=', '!=', '>', '>=', '<', '<='])) {
                    throw new ArgumentException(
                        "Invalid conditional relation symbol.",
                        Eorm::ERROR_ARGUMENT
                    );
                }
                $this->push($field . $option . '?');
            } else {
                throw new ArgumentException(
                    "Invalid conditional relation symbol.",
                    Eorm::ERROR_ARGUMENT
                );
            }
        } elseif (is_array($value)) {
            $count = count($value);
            if (!$count) {
                throw new ArgumentException(
                    "Condition value cannot be an empty array.",
                    Eorm::ERROR_ARGUMENT
                );
            }
            $this->buffer()->pushMany($value);
            if ($count === 1) {
                if ($option) {
                    $this->push($field . '=?');
                } else {
                    $this->push($field . '!=?');
                }
            } else {
                if ($option) {
                    $this->push($field . ' IN ' . Helper::fill($count));
                } else {
                    $this->push($field . ' NOT IN ' . Helper::fill($count));
                }
            }
        } elseif (is_null($value)) {
            if ($option) {
                return $this->push($field . ' IS NULL');
            } else {
                return $this->push($field . ' IS NOT NULL');
            }
        } else {
            throw new ArgumentException('Invalid condition value.', Eorm::ERROR_ARGUMENT);
        }

        return $this;
    }

    /**
     * Add a SQL where like condition.
     *
     * @param  string  $field   [description]
     * @param  string  $value   [description]
     * @param  boolean $option  [description]
     * @return Where
     */
    public function like($field, $value, $option = true)
    {
        if (is_string($value)) {
            $formattedField = Helper::format($field);
            if ($option) {
                $this->push($formattedField . ' LIKE ?');
            } else {
                $this->push($formattedField . ' NOT LIKE ?');
            }
        } else {
            throw new ArgumentException('Invalid like condition value.', Eorm::ERROR_ARGUMENT);
        }

        return $this;
    }

    /**
     * [group description]
     * @param  Closure $closure [description]
     * @param  boolean $mode    [description]
     * @return [type]           [description]
     */
    public function group(Closure $closure, $mode = false)
    {
        $where = new Where($mode);

        $closure($where);

        $this->push($where->build(true));
        $this->buffer()->merge($where->buffer());

        return $this;
    }

    /**
     * Gets the parameter buffer component instanse.
     *
     * @return BufferInterface
     */
    public function buffer()
    {
        return $this->buffer;
    }

    /**
     * Build and return SQL where condition.
     *
     * @param  boolean  $subset  Whether as a subset to build ? (no)
     * @return string
     */
    public function build($subset = false)
    {
        $condition = implode($this->mode ? ' AND ' : ' OR ', $this->conditions);

        if ($subset) {
            return '(' . $condition . ')';
        } else {
            return $condition;
        }
    }

    /**
     * Push a SQL where condition to conditions cache.
     *
     * @param  string  $condition  The SQL where condition.
     * @return Where
     */
    protected function push($condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }
}
