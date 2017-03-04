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
namespace Eorm\Event;

/**
 * Eorm event body class.
 */
class Body
{
    /**
     * The Eorm event name.
     *
     * @var string
     */
    private $name;

    /**
     * The Eorm event body contains all properties.
     *
     * @var array
     */
    private $properties;

    /**
     * Initialize this Eorm event body instanse.
     *
     * @param string  $name        The Eorm event name.
     * @param array   $properties  The Eorm event body contains all properties.
     */
    public function __construct($name, $builder, array $properties = [])
    {
        $this->name       = $name;
        $this->properties = $properties;
    }

    /**
     * Gets the Eorm event name.
     *
     * @return string
     */
    public function name()
    {

        return $this->name;
    }

    /**
     * Gets the Eorm event body properties by name.
     * If the property does not exist, the default value is returned.
     *
     * @param  string  $name     The Eorm event body property name.
     * @param  mixed   $default  The default value. (NULL)
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->exists($name)) {
            return $this->properties[$name];
        } else {
            return $default;
        }
    }

    /**
     * Check whether the Eorm event body property exists.
     *
     * @param  string  $name  The Eorm event body property name.
     * @return boolean
     */
    public function exists($name)
    {

        return array_key_exists($name, $this->properties);
    }

    /**
     * Magic Method.
     * Gets the Eorm event body properties by name.
     * If the property does not exist, the NULL is returned.
     *
     * @param  string  $name  The Eorm event body property name.
     * @return mixed
     */
    public function __get($name)
    {

        return $this->get($name);
    }

    /**
     * Magic Method.
     * Check whether the Eorm event body property exists.
     *
     * @param  string  $name  The Eorm event body property name.
     * @return boolean
     */
    public function __isset($name)
    {

        return $this->exists($name);
    }
}
