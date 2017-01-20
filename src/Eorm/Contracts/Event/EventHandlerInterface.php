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
 * Eorm event handler class interface.
 */
interface EventHandlerInterface
{
    /**
     * Execute Eorm event handler.
     *
     * @param  EventBodyInterface  $event  The Eorm event body instanse.
     * @return boolean
     */
    public function handle(EventBodyInterface $event);
}
