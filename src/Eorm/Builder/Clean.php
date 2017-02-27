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

/**
 *
 */
class Clean extends Basic
{
    /**
     * [$type description]
     *
     * @var string
     */
    protected static $type = 'clean';

    /**
     * [build description]
     * @return [type] [description]
     */
    public function build()
    {
        return 'TRUNCATE TABLE ' . $this->actuator()->table();
    }
}
