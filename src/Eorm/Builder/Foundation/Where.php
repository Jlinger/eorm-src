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
use Eorm\Exceptions\EormException;
use Eorm\Foundation\Parameter;
use Eorm\Library\Helper;

/**
 * SQL where condition builder class.
 */
class Where
{
    /**
     * [$mode description]
     * @var boolean
     */
    protected $mode = true;

    /**
     * [$parameter description]
     * @var [type]
     */
    protected $parameter;

    /**
     * [$conditions description]
     * @var array
     */
    protected $conditions = [];

    /**
     * [__construct description]
     * @param Parameter $parameter [description]
     */
    public function __construct(Parameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * [setMode description]
     * @param [type] $mode [description]
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * [getMode description]
     * @return [type] [description]
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        return implode(
            $this->getMode() ? ' AND ' : ' OR ',
            $this->conditions
        );
    }

    /**
     * [condition description]
     * @param  [type] $field  [description]
     * @param  [type] $value  [description]
     * @param  [type] $option [description]
     * @return [type]         [description]
     */
    public function condition($field, $value, $option)
    {
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            if (is_bool($option)) {
                return $option ? $this->equal($target, $value) : $this->notEqual($target, $value);
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
            throw new EormException('Invalid conditional connection.', Eorm::ERROR_ARGUMENT);
        } elseif (is_array($value)) {
            if (!empty($value)) {
                return $option ? $this->inArray($target, $value) : $this->notInArray($target, $value);
            }
            throw new EormException('Condition value cannot be an empty array.', Eorm::ERROR_ARGUMENT);
        } elseif (is_null($value)) {
            return $option ? $this->isNull($target) : $this->isNotNull($target);
        } else {
            throw new EormException('Illegal condition value.', Eorm::ERROR_ARGUMENT);
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
        $this->conditions[] = Helper::format($field) . '=?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [notEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function notEqual($field, $value)
    {
        $this->conditions[] = Helper::format($field) . '!=?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [greater description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function greater($field, $value)
    {
        $this->conditions[] = Helper::format($field) . '>?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [greaterEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function greaterEqual($field, $value)
    {
        $this->conditions[] = Helper::format($field) . '>=?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [less description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function less($field, $value)
    {
        $this->conditions[] = Helper::format($field) . '<?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [lessEqual description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function lessEqual($field, $value)
    {
        $this->conditions[] = Helper::format($field) . '<=?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [inArray description]
     * @param  [type] $field [description]
     * @param  array  $value [description]
     * @return [type]        [description]
     */
    public function inArray($field, array $value)
    {
        $this->conditions[] = Helper::format($field) . ' IN ' . Helper::fill(count($value));
        $this->parameter->pushMany($value);

        return $this;
    }

    /**
     * [notInArray description]
     * @param  [type] $field [description]
     * @param  array  $value [description]
     * @return [type]        [description]
     */
    public function notInArray($field, array $value)
    {
        $this->conditions[] = Helper::format($field) . ' NOT IN ' . Helper::fill(count($value));
        $this->parameter->pushMany($value);

        return $this;
    }

    /**
     * [isNull description]
     * @param  [type]  $field [description]
     * @return boolean        [description]
     */
    public function isNull($field)
    {
        $this->conditions[] = Helper::format($field) . ' IS NULL';

        return $this;
    }

    /**
     * [isNotNull description]
     * @param  [type]  $field [description]
     * @return boolean        [description]
     */
    public function isNotNull($field)
    {
        $this->conditions[] = Helper::format($field) . ' IS NOT NULL';

        return $this;
    }

    /**
     * [like description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function like($field, $value)
    {
        $this->conditions[] = Helper::format($field) . ' LIKE ?';
        $this->parameter->push($value);

        return $this;
    }

    /**
     * [notLike description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function notLike($field, $value)
    {
        $this->conditions[] = Helper::format($field) . ' NOT LIKE ?';
        $this->parameter->push($value);

        return $this;
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
        $where->setMode(true);
        $this->conditions[] = '(' . $where->build() . ')';

        return $this;
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
        $where->setMode(false);
        $this->conditions[] = '(' . $where->build() . ')';

        return $this;
    }
}
