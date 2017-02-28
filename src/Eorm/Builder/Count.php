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

/**
 *
 */
class Count extends Select
{
    /**
     * [$type description]
     *
     * @var string
     */
    protected $type = 'count';

    /**
     * [build description]
     *
     * @return string
     */
    public function build()
    {
        $table = $this->formatTable();
        $filed = $this->formatPrimaryKey();

        $statement = "SELECT COUNT({$filed}) AS `eorm_count` FROM {$table}";
        if ($this->where) {
            $statement .= ' WHERE ' . $this->where->build();
        }

        return $statement;
    }
}
