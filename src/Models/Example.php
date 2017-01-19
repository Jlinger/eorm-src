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
namespace Models;

use Eorm\Eorm;

/**
 *
 */
class Example extends Eorm
{
    /**
     * [$table description]
     * @var null
     */
    protected $table = null;

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * [$server description]
     * @var string
     */
    protected $server = 'eorm';
}
