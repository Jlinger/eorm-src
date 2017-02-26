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
use Eorm\Builder\Foundation\Traits\Condition;
use Eorm\Builder\Foundation\Traits\Limit;

/**
 *
 */
class Select extends BuilderAbstract
{
    use Condition, Limit;

    /**
     * [$type description]
     *
     * @var string
     */
    protected static $type = 'select';

    /**
     * [$fields description]
     * @var array
     */
    protected $fields = [];

    /**
     * [$orderBy description]
     * @var array
     */
    protected $orderBy = [];

    /**
     * [$skip description]
     * @var integer
     */
    protected $skip = 0;

    /**
     * [field description]
     * @param  [type] $field [description]
     * @param  [type] $alias [description]
     * @return [type]        [description]
     */
    public function field($field, $alias = null)
    {
        if (empty($this->fields)) {
            $primaryKey = $this->actuator()->primaryKey(false);
            if ($field !== $primaryKey) {
                $this->fields[$primaryKey] = $this->actuator()->primaryKey();
            }
        }

        if (is_null($alias)) {
            $this->fields[$field] = Helper::format($field);
        } else {
            $this->fields[$alias] = Helper::format($field) . ' AS ' . Helper::format($alias);
        }

        return $this;
    }

    /**
     * [orderBy description]
     * @param  [type] $field  [description]
     * @param  [type] $ascend [description]
     * @return [type]         [description]
     */
    public function orderBy($field, $ascend)
    {
        $this->orderBy[$field] = Helper::format($field) . ' ' . ($ascend ? 'ASC' : 'DESC');

        return $this;
    }

    /**
     * [skip description]
     * @param  [type] $count [description]
     * @return [type]        [description]
     */
    public function skip($count)
    {
        $this->skip = $count;

        return $this;
    }

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        $table = $this->actuator()->table();
        if (empty($this->fields)) {
            $filed = '*';
        } else {
            $filed = implode(',', $this->fields);
        }

        $statement = "SELECT {$filed} FROM {$table}";
        if ($this->where) {
            $statement .= ' WHERE ' . $this->where->build();
        }
        if (!empty($this->orderBy)) {
            $statement .= ' ORDER BY ' . implode(',', $this->orderBy);
        }
        if ($this->limit) {
            if ($this->skip) {
                $statement .= ' LIMIT ' . $this->skip . ',' . $this->limit;
            } else {
                $statement .= ' LIMIT ' . $this->limit;
            }
        }

        return $statement;
    }
}
