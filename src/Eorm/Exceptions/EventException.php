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
namespace Eorm\Exceptions;

use Eorm\Contracts\Exceptions\EventExceptionInterface;

/**
 * The Eorm event exception class.
 */
class EventException extends EormException implements EventExceptionInterface
{
    /**
     * The exception event name.
     *
     * @var string
     */
    protected $event;

    /**
     * Construct the statement exception.
     *
     * @param string   $message  The exception message.
     * @param integer  $code     The exception code.
     * @param string   $event    The exception event name.
     */
    public function __construct($message, $code, $event)
    {
        $this->event = $event;

        parent::__construct($message, $code);
    }

    /**
     * Gets the Exception event name.
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }
}
