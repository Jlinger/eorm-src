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

use Eorm\Builder\Foundation\Basic;
use Eorm\Builder\Foundation\Traits\Astriction;
use Eorm\Builder\Foundation\Traits\Condition;

/**
 *
 */
class Select extends Basic
{
    use Condition, Astriction;

    /**
     * The SQL statement builder type name.
     *
     * @var string
     */
    protected $type = 'select';

    /**
     * Query field list.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Sort field list.
     *
     * @var array
     */
    protected $orderBy = [];

    /**
     * Result set ignored row count.
     *
     * @var integer
     */
    protected $skip = 0;

    /**
     * [field description]
     * @param  [type] $field [description]
     * @param  [type] $alias [description]
     * @return Select
     */
    public function field($field)
    {
        $this->fields[$field] = $this->format($field);

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
        if ($ascend) {
            $this->orderBy[$field] = $this->format($field) . ' ASC';
        } else {
            $this->orderBy[$field] = $this->format($field) . ' DESC';
        }

        return $this;
    }

    /**
     * [skip description]
     * @param  [type] $count [description]
     * @return [type]        [description]
     */
    public function skip($count)
    {
        $this->skip = (int) $count;

        return $this;
    }

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        $table = $this->formatTable();
        if (empty($this->fields)) {
            $filed = '*';
        } else {
            $filed = $this->join($this->fields);
        }

        $statement = "SELECT {$filed} FROM {$table}";
        if ($this->where) {
            $statement .= ' WHERE ' . $this->where->build();
        }
        if (!empty($this->orderBy)) {
            $statement .= ' ORDER BY ' . $this->join($this->orderBy);
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
