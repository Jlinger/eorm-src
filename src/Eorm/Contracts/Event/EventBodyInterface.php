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
namespace Eorm\Contracts\Event;

/**
 * Eorm event body class interface.
 */
interface EventBodyInterface
{
    /**
     * Gets the Eorm event name.
     *
     * @return string
     */
    public function name();

    /**
     * Gets the Eorm event body properties by name.
     * If the property does not exist, the default value is returned.
     *
     * @param  string  $name     The Eorm event body property name.
     * @param  mixed   $default  The default value. (NULL)
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Check whether the Eorm event body property exists.
     *
     * @param  string  $name  The Eorm event body property name.
     * @return boolean
     */
    public function exists($name);
}
