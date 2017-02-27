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
 * This Is A Test Model Class.
 */
class Example extends Eorm
{
    /**
     * The database table name.
     *
     * @var null
     */
    protected $table = null;

    /**
     * The database table primary key name.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The database server name.
     *
     * @var string
     */
    protected $server = 'eorm';
}
