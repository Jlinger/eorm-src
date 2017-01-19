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
namespace Eorm\Library;

use Closure;
use Eorm\Exceptions\EormException;

/**
 *
 */
class Where
{
    /**
     * [$mode description]
     * @var [type]
     */
    protected $mode;

    /**
     * [$conditions description]
     * @var array
     */
    protected $conditions = [];

    /**
     * [$arguments description]
     * @var array
     */
    protected $arguments = [];

    /**
     * [__construct description]
     * @param boolean $mode [description]
     */
    public function __construct($mode = true)
    {
        $this->mode($mode);
    }

    /**
     * [mode description]
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    public function mode($mode)
    {
        $this->mode = (bool) $mode;
        return $this;
    }

    /**
     * [clean description]
     * @return [type] [description]
     */
    public function clean()
    {
        $this->conditions = [];
        return $this;
    }

    /**
     * [compare description]
     * @param  [type]  $field  [description]
     * @param  [type]  $value  [description]
     * @param  boolean $option [description]
     * @return [type]          [description]
     */
    public function compare($field, $value, $option = true)
    {
        $field = Helper::format($field);

        if (is_string($value) || is_numeric($value)) {
            if (is_bool($option)) {
                if ($option) {
                    $this->pushCondition($field . '=?');
                } else {
                    $this->pushCondition($field . '!=?');
                }
            } elseif (is_string($option)) {
                $option = trim($option);
                if (!in_array($option, ['=', '!=', '>', '>=', '<', '<='])) {
                    throw new EormException("Invalid conditional connection.");
                }
                $this->pushCondition($field . $option . '?');
            } else {
                throw new EormException("Invalid conditional connection.");
            }
            return $this->pushArgument($value);
        }

        if (is_array($value)) {
            if ($length = count($value)) {
                if ($length > 1) {
                    if ($option) {
                        $this->pushCondition($field . ' IN ' . Helper::fill($length));
                    } else {
                        $this->pushCondition($field . ' NOT IN ' . Helper::fill($length));
                    }
                } else {
                    if ($option) {
                        $this->pushCondition($field . '=?');
                    } else {
                        $this->pushCondition($field . '!=?');
                    }
                }
                return $this->pushArgument($value);
            } else {
                throw new EormException('Condition value cannot be an empty array.');
            }
        }

        if (is_null($value)) {
            if ($option) {
                return $this->pushCondition($field . ' IS NULL');
            } else {
                return $this->pushCondition($field . ' IS NOT NULL');
            }
        }

        throw new EormException('Illegal condition value.');
    }

    /**
     * [like description]
     * @param  [type]  $field  [description]
     * @param  [type]  $value  [description]
     * @param  boolean $option [description]
     * @return [type]          [description]
     */
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

        $this->pushCondition($where->toString(true));

        $arguments = $where->getArgument();
        if (!empty($arguments)) {
            $this->pushArgument($arguments);
        }

        return $this;
    }

    /**
     * [getArgument description]
     * @return [type] [description]
     */
    public function getArgument()
    {
        return $this->arguments;
    }

    /**
     * [toString description]
     * @param  boolean $brackets [description]
     * @return [type]            [description]
     */
    public function toString($brackets = false)
    {
        $condition = implode($this->mode ? ' AND ' : ' OR ', $this->conditions);

        if ($brackets) {
            return '(' . $condition . ')';
        } else {
            return $condition;
        }
    }

    /**
     * [pushArgument description]
     * @param  [type] $argument [description]
     * @return [type]           [description]
     */
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

    /**
     * [pushCondition description]
     * @param  [type] $condition [description]
     * @return [type]            [description]
     */
    protected function pushCondition($condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }
}
