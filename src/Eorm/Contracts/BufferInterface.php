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
 * Eorm SQL statement parameter buffer component interface.
 */
interface BufferInterface
{
    /**
     * Push the binding parameters of the SQL statement.
     * The binding parameter of the SQL statement must be a scalar.
     *
     * @param  array|string|number|boolean|null  $parameters  The binding parameters of SQL statement.
     * @return Buffer
     */
    public function push($parameters);

    /**
     * Gets the number of binding parameters that have been added.
     *
     * @return integer
     */
    public function count();

    /**
     * Output all binding parameters.
     *
     * @param  boolean  $clean  Clean all binding parameters ? (no)
     * @return array
     */
    public function output($clean = false);

    /**
     * Clean all binding parameters.
     *
     * @return Buffer
     */
    public function clean();
}
