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

use Eorm\Contracts\Event\EventBodyInterface;
use Eorm\Contracts\Event\EventHandlerInterface;

/**
 * Eorm event manager class interface.
 */
interface EventInterface
{
    /**
     * Register a Eorm event handler.
     *
     * @param  string                 $name     The Eorm event name.
     * @param  EventHandlerInterface  $handler  The Eorm event handler.
     * @return integer
     */
    public static function on($name, EventHandlerInterface $handler);

    /**
     * Delete a Eorm event handler.
     *
     * @param  string   $name  The Eorm event name.
     * @param  boolean  $all   Delete all event handlers ? (no)
     * @return EventHandlerInterface|array|null
     */
    public static function off($name, $all = false);

    /**
     * Trigger a Eorm event.
     *
     * @param  EventBodyInterface  $body  The Eorm event body instanse.
     * @return boolean
     */
    public static function trigger(EventBodyInterface $body);

    /**
     * Check whether a Eorm event has a handler.
     * For an invalid Eorm event name, the method returns false.
     *
     * @param  string  $name  The Eorm event name.
     * @return boolean
     */
    public static function exists($name);
}
