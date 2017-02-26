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

use Eorm\Builder\Foundation\BuilderAbstract;
use Eorm\Library\Helper;

/**
 *
 */
class Insert extends BuilderAbstract
{
    /**
     * [$type description]
     *
     * @var string
     */
    protected static $type = 'insert';

    /**
     * [$columnData description]
     * @var array
     */
    protected $columnData = [];

    /**
     * [set description]
     * @param [type] $field [description]
     * @param [type] $value [description]
     */
    public function set($field, $value)
    {
        if (!isset($this->columnData[$field])) {
            $this->columnData[$field] = [];
        }

        if (is_array($value)) {
            foreach ($value as $unit) {
                $this->columnData[$field][] = $unit;
            }
        } else {
            $this->columnData[$field][] = $value;
        }

        return $this;
    }

    /**
     * [enable description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function enable($field)
    {
        return $this->set($field, 1);
    }

    /**
     * [disable description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function disable($field)
    {
        return $this->set($field, 0);
    }

    /**
     * [touch description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function touch($field)
    {
        return $this->set($field, time());
    }

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        $table   = $this->actuator()->table();
        $fields  = [];
        $columns = [];
        $maximum = 0;
        foreach ($this->columnData as $key => $value) {
            $fields[]  = Helper::format($key);
            $columns[] = $value;
            $count     = count($value);
            if ($count > $maximum) {
                $maximum = $count;
            }
        }
        if ($maximum > 1) {
            $columns = array_map(
                function ($column) use ($maximum) {
                    return array_pad($column, $maximum, end($column));
                },
                $columns
            );
        }

        $field  = implode(',', $fields);
        $unit   = Helper::fill(count($columns));
        $values = implode(',', array_map(function (...$row) use ($unit) {
            $this->parameter()->pushMany($row);
            return $unit;
        }, ...$columns));

        return "INSERT INTO {$table} ({$field}) VALUES {$values}";
    }
}
