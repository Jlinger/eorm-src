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

/**
 *
 */
trait Limit
{
    /**
     * [$limit description]
     *
     * @var integer
     */
    protected $limit = 0;

    /**
     * [limit description]
     *
     * @param  integer  $count  [description]
     * @return Eorm\Builder\Foundation\BuilderAbstract
     */
    public function limit($count)
    {
        $this->limit = (int) $count;

        return $this;
    }
}
