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
trait Astriction
{
    /**
     * The maximum row count.
     *
     * @var integer
     */
    protected $limit = 0;

    /**
     * Set the maximum row count affected.
     *
     * @param  integer  $count  The maximum row count.
     * @return Basic
     */
    public function limit($count)
    {
        $this->limit = (int) $count;

        return $this;
    }
}
