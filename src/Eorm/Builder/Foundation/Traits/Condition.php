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
namespace Eorm\Builder\Foundation\Traits;

use Eorm\Builder\Foundation\Where;
use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 *
 */
trait Condition
{
    /**
     * SQL where condition builder instance.
     *
     * @var Where
     */
    protected $where = null;

    /**
     * Set filter conditions.
     *
     * @param  mixed    $target  Targets for filtering conditions.
     * @param  mixed    $value   Conditional value.
     * @param  boolean  $option  Relationship constraint option.
     * @param  boolean  $mode    Top level connection mode.
     * @return Basic
     */
    public function where($target, $value = null, $option = true, $mode = true)
    {
        if (is_null($this->where)) {
            $this->where = new Where($this->parameter);
            $this->where->setMode($mode);
        }

        if (is_string($target)) {
            $this->where->condition($target, $value, $option);
        } elseif (is_array($target)) {
            if (empty($target)) {
                throw new EormException(
                    'Condition target cannot be an empty array.',
                    Eorm::ERROR_ARGUMENT
                );
            } else {
                foreach ($target as $field => $rule) {
                    $this->where->condition($field, $rule, $option);
                }
            }
        } elseif ($target instanceof Closure) {
            $target($this->where, $value);
        } else {
            throw new EormException(
                'Illegal condition target.',
                Eorm::ERROR_ARGUMENT
            );
        }

        return $this;
    }
}
