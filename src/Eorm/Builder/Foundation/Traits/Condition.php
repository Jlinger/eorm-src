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
     * [$where description]
     *
     * @var Eorm\Builder\Foundation\Where
     */
    protected $where = null;

    /**
     * [where description]
     *
     * @param  mixed    $target  [description]
     * @param  mixed    $value   [description]
     * @param  boolean  $option  [description]
     * @param  boolean  $mode    [description]
     * @return Eorm\Builder\Foundation\BuilderAbstract
     */
    public function where($target, $value = null, $option = true, $mode = true)
    {
        if (is_null($this->where)) {
            $this->where = new Where($this->parameter());
            $this->where->setMode($mode);
        }

        if (is_string($target)) {
            $this->where->condition($target, $value, $option);
        } elseif (is_array($target)) {
            if (empty($target)) {
                throw new EormException('Condition target cannot be an empty array.', Eorm::ERROR_ARGUMENT);
            } else {
                foreach ($target as $field => $rule) {
                    $this->where->condition($field, $rule, $option);
                }
            }
        } elseif ($target instanceof Closure) {
            $target($this->where, $value);
        } else {
            throw new EormException('Illegal condition target.', Eorm::ERROR_ARGUMENT);
        }

        return $this;
    }
}
