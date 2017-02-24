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
namespace Eorm\Foundation;

use Eorm\Contracts\Event\EventBodyInterface;

/**
 * Eorm event handler abstract class.
 */
abstract class EventHandlerAbstract
{
    /**
     * The Eorm event name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Gets Eorm event name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Execute Eorm event handler.
     *
     * @param  EventBodyInterface  $event  The Eorm event body instanse.
     * @return boolean
     */
    abstract public function handle(EventBodyInterface $event);
}
