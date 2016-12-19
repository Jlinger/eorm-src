<?php
/**
 *+------------------------------------------------------------------------------------------------+
 *| Edoger ORM                                                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| A Simple PHP Object Relational Mapping Library.                                                |
 *+------------------------------------------------------------------------------------------------+
 *| @license   MIT                                                                                 |
 *| @link      https://www.edoger.com/                                                             |
 *| @copyright Copyright (c) 2016 Qingshan Luo                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| @author    Qingshan Luo <shanshan.lqs@gmail.com>                                               |
 *+------------------------------------------------------------------------------------------------+
 */
namespace Eorm\Library;

use Closure;
use Eorm\Exceptions\EormException;
use Eorm\Library\Helper;

class Where
{
    protected $mode;
    protected $conditions = [];
    protected $arguments  = [];

    public function __construct($mode = true)
    {
        $this->mode = $mode;
    }

    protected function pushArgument($argument)
    {
        if (is_array($argument)) {
            foreach ($argument as $value) {
                $this->arguments[] = $value;
            }
        } else {
            $this->arguments[] = $argument;
        }

        return $this;
    }

    protected function pushCondition($condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function compare($field, $value, $option = true)
    {
        $field = Helper::standardise($field);
        if (is_string($value) || is_numeric($value)) {
            if (is_bool($option)) {
                $connector = $option ? '=' : '!=';
            } elseif (is_string($option)) {
                $connector = trim($option);
                if (!in_array($connector, ['=', '!=', '>', '>=', '<', '<='])) {
                    throw new EormException("Invalid condition connection.");
                }
            } else {
                throw new EormException("Illegal condition connection.");
            }
            $this->pushCondition($field . $connector . '?')->pushArgument($value);
        } elseif (is_array($value)) {
            if (empty($value)) {
                throw new EormException('Condition cannot be an empty array.');
            }
            $this->pushCondition(
                $field . ($option ? ' IN ' : ' NOT IN ') . Helper::fill(count($value))
            )->pushArgument($value);
        } elseif (is_null($value)) {
            $this->pushCondition($field . ($option ? ' IS NULL' : ' IS NOT NULL'));
        } else {
            throw new EormException('Illegal condition value.');
        }

        return $this;
    }

    public function like($field, $value, $option = true)
    {
        return $this->pushCondition(
            Helper::standardise($field) . ($option ? ' LIKE ?' : ' NOT LIKE ?')
        )->pushArgument($value);
    }

    public function group(Closure $closure, $mode = false)
    {
        $where = new Where($mode);
        $closure($where);

        return $this->pushCondition('(' . $where->toString() . ')')->pushArgument($where->getArgument());
    }

    public function getArgument()
    {
        return $this->arguments;
    }

    public function toString()
    {
        return empty($this->conditions) ? '' : implode($this->mode ? ' AND ' : ' OR ', $this->conditions);
    }
}
