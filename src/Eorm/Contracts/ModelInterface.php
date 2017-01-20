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
namespace Eorm\Contracts;

/**
 * Eorm model base class interface.
 */
interface ModelInterface
{
    /**
     * Gets the model associated database table name.
     *
     * @return string
     */
    public function getTable();

    /**
     * Gets model associated database table primary key name.
     *
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Gets model associated database server name.
     *
     * @return string
     */
    public function getServer();

    /**
     * Get the current model using database table name.
     *
     * @return string
     */
    public static function table();

    /**
     * Get the current model using database table primary key name.
     *
     * @return string
     */
    public static function primaryKey();
}
