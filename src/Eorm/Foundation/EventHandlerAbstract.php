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
     * Initialize this event handler instanse.
     *
     * @param string  $name  The Eorm event name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

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
     * @param  Eorm\Foundation\Body  $event  The Eorm event body instanse.
     * @return boolean
     */
    abstract public function handle(Body $body);
}
