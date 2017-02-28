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

use Closure;
use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 * SQL where condition builder class.
 */
class Where
{
    /**
     * Conditional connection mode.
     *
     * @var boolean
     */
    protected $mode = true;

    /**
     * The SQL statement parameter manager instance.
     *
     * @var Parameter
     */
    protected $parameter;

    /**
     * Filtration conditions.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Initializes SQL where condition builder,
     * and injecting the necessary dependency resources.
     *
     * @param  Parameter  $parameter  The SQL statement parameter manager instance.
     * @return void
     */
    public function __construct(Parameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Set conditional connection mode.
     *
     * @param  boolean  $mode  The conditional connection mode.
     * @return Where
     */
    public function setMode($mode)
    {
        $this->mode = (bool) $mode;

        return $this;
    }

    /**
     * Gets conditional connection mode.
     *
     * @return boolean
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Build SQL where condition.
     *
     * @return string
     */
    public function build()
    {
        $connector = $this->getMode() ? ' AND ' : ' OR ';

        return implode($connector, $this->conditions);
    }

    /**
     * Append a SQL filter condition.
     *
     * @param  string  $field   The table field name.
     * @param  mixed   $value   Conditional value.
     * @param  mixed   $option  Relationship constraint option.
     * @return Where
     */
    public function condition($field, $value, $option)
    {
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            if (is_bool($option)) {
                $option = $option ? '=' : '!=';
            }

            switch ($option) {
                case '=':
                    return $this->equal($target, $value);
                case '!=':
                    return $this->notEqual($target, $value);
                case '>':
                    return $this->greater($target, $value);
                case '>=':
                    return $this->greaterEqual($target, $value);
                case '<':
                    return $this->less($target, $value);
                case '<=':
                    return $this->lessEqual($target, $value);
                case '~=':
                    return $this->like($target, $value);
                case '!~=':
                    return $this->notLike($target, $value);
            }

            throw new EormException(
                'Invalid conditional connection.',
                Eorm::ERROR_ARGUMENT
            );
        } elseif (is_array($value)) {
            if ($option) {
                return $this->inArray($target, $value);
            } else {
                return $this->notInArray($target, $value);
            }
        } elseif (is_null($value)) {
            if ($option) {
                return $this->isNull($target);
            } else {
                return $this->isNotNull($target);
            }
        } else {
            throw new EormException(
                'Illegal condition value.',
                Eorm::ERROR_ARGUMENT
            );
        }
    }

    /**
     * [equal description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function equal($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '=?')
            ->pushParameter($value);
    }

    /**
     * [notEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function notEqual($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '!=?')
            ->pushParameter($value);
    }

    /**
     * [greater description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function greater($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '>?')
            ->pushParameter($value);
    }

    /**
     * [greaterEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function greaterEqual($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '>=?')
            ->pushParameter($value);
    }

    /**
     * [less description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function less($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '<?')
            ->pushParameter($value);
    }

    /**
     * [lessEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function lessEqual($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . '<=?')
            ->pushParameter($value);
    }

    /**
     * [inArray description]
     * @param  [type] $field [description]
     * @param  array  $value [description]
     * @return [type]        [description]
     */
    public function inArray($field, array $value)
    {
        $count = count($value);

        if (!$count) {
            throw new EormException(
                'Condition value cannot be an empty array.',
                Eorm::ERROR_ARGUMENT
            );
        }

        return $this
            ->pushCondition($this->format($field) . ' IN ' . $this->fill($count))
            ->pushParameter($value);
    }

    /**
     * [notInArray description]
     * @param  [type] $field [description]
     * @param  array  $value [description]
     * @return [type]        [description]
     */
    public function notInArray($field, array $value)
    {
        $count = count($value);

        if (!$count) {
            throw new EormException(
                'Condition value cannot be an empty array.',
                Eorm::ERROR_ARGUMENT
            );
        }

        return $this
            ->pushCondition($this->format($field) . ' NOT IN ' . $this->fill($count))
            ->pushParameter($value);
    }

    /**
     * [isNull description]
     * @param  [type]  $field [description]
     * @return boolean        [description]
     */
    public function isNull($field)
    {
        return $this->pushCondition($this->format($field) . ' IS NULL');
    }

    /**
     * [isNotNull description]
     * @param  [type]  $field [description]
     * @return boolean        [description]
     */
    public function isNotNull($field)
    {
        return $this->pushCondition($this->format($field) . ' IS NOT NULL');
    }

    /**
     * [like description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function like($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . ' LIKE ?')
            ->pushParameter($value);
    }

    /**
     * [notLike description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function notLike($field, $value)
    {
        return $this
            ->pushCondition($this->format($field) . ' NOT LIKE ?')
            ->pushParameter($value);
    }

    /**
     * [groupAnd description]
     * @param  Closure $closure [description]
     * @param  [type]  $option  [description]
     * @return [type]           [description]
     */
    public function groupAnd(Closure $closure, $option = null)
    {
        $where = new Where($this->parameter);

        $closure($where, $option);

        return $this->pushCondition('(' . $where->setMode(true)->build() . ')');
    }

    /**
     * [groupOr description]
     * @param  Closure $closure [description]
     * @param  [type]  $option  [description]
     * @return [type]           [description]
     */
    public function groupOr(Closure $closure, $option = null)
    {
        $where = new Where($this->parameter);

        $closure($where, $option);

        return $this->pushCondition('(' . $where->setMode(false)->build() . ')');
    }

    /**
     * [format description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    protected function format($field)
    {
        if (is_string($field)) {
            return '`' . str_replace('`', '``', $field) . '`';
        } else {
            throw new EormException(
                'Field name must be a string.',
                Eorm::ERROR_ARGUMENT
            );
        }
    }

    /**
     * [fill description]
     * @param  [type] $count [description]
     * @return [type]        [description]
     */
    protected function fill($count)
    {
        return '(' . implode(',', array_fill(0, $count, '?')) . ')';
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

    /**
     * [pushParameter description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    protected function pushParameter($param)
    {
        $this->parameter->push($param);

        return $this;
    }
}
