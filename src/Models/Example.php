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

use Eorm\Model;

/**
 * This is a Eorm example model class for testing.
 */
class Example extends Model
{
    /**
     * Model associated database table name.
     * If set to NULL or not set, will use the model class name to lowercase as the default table name.
     *
     * @var string|null
     */
    protected $table = null;

    /**
     * Model associated database table primary key name.
     * If not set, will use 'id' as the default primary key name.
     * We agree that the primary key field must be self increasing.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Model associated database server name.
     * If not set, will use 'default' as the default database server name.
     *
     * @var string
     */
    protected $server = 'eorm';
}
