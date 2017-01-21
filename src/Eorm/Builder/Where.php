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
 *
 */
class Where
{
    protected $mode;
    protected $buffer;

    protected $conditions = [];

    public function __construct($mode = true)
    {
        $this->mode   = boolval($mode);
        $this->buffer = new Buffer();
    }

    public function mode($mode = null)
    {
        if (is_bool($mode)) {
            $this->mode = $mode;
        }

        return $this->mode;
    }

    public function clean()
    {
        $this->conditions = [];
        $this->buffer()->clean();

        return $this;
    }

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

    public function like($field, $value, $option = true)
    {
        $field = Helper::format($field);

        if (preg_match('/[%_]/', $value)) {
            $connector = $option ? ' LIKE ?' : ' NOT LIKE ?';
        } else {
            $connector = $option ? '=?' : '!=?';
        }

        return $this->pushCondition($field . $connector)->pushArgument($value);
    }

    public function group(Closure $closure, $mode = false)
    {
        $where = new Where($mode);
        $closure($where);

        $this->pushCondition($where->toString(true));

        $arguments = $where->getArgument();
        if (!empty($arguments)) {
            $this->pushArgument($arguments);
        }

        return $this;
    }

    public function buffer()
    {
        return $this->buffer;
    }

    public function make($brackets = false)
    {
        $condition = implode($this->mode ? ' AND ' : ' OR ', $this->conditions);

        if ($brackets) {
            return '(' . $condition . ')';
        } else {
            return $condition;
        }
    }

    protected function push($condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }
}
