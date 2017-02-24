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
namespace Eorm;

use Eorm\Exceptions\EventException;
use Exception;
use Throwable;

/**
 * Eorm event manager class.
 */
class Event
{
    /**
     * All registered Eorm event handlers.
     *
     * @var array
     */
    private $handlers = [
        'select'  => [],
        'update'  => [],
        'insert'  => [],
        'delete'  => [],
        'replace' => [],
        'clean'   => [],
        'count'   => [],
        'exists'  => [],
        'execute' => [],
    ];

    /**
     * Check whether a Eorm event has a handler.
     * For an invalid Eorm event name, the method returns false.
     *
     * @param  string  $name  The Eorm event name.
     * @return boolean
     */
    public function exist($name)
    {
        return !empty($this->handlers[$name]);
    }

    /**
     * Register Eorm event handler.
     *
     * @param  EventHandlerAbstract  $handler  The Eorm event handler.
     * @return boolean
     */
    public function bind(EventHandlerAbstract $handler)
    {
        $name = $handler->name();
        if ($name && isset($this->handlers[$name])) {
            array_unshift($this->handlers[$name], $handler);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete Eorm event handler.
     *
     * @param  string  $name  The Eorm event name.
     * @return boolean
     */
    public function unbind($name)
    {
        if (isset($this->handlers[$name])) {
            $this->handlers[$name] = [];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Trigger a Eorm event.
     *
     * @param  EventBodyInterface  $body  The Eorm event body instanse.
     * @return boolean
     */
    public function trigger(EventBody $body)
    {
        $name = $body->name();
        if (!$this->exist($name)) {
            return true;
        }

        try {
            foreach ($this->handlers[$name] as $handler) {
                if ($handler->handle($body) !== true) {
                    break;
                }
            }
            return true;
        } catch (EventException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new EventException($e->getMessage(), Eorm::ERROR_EVENT, $name);
        } catch (Throwable $e) {
            throw new EventException($e->getMessage(), Eorm::ERROR_EVENT, $name);
        }

        return false;
    }
}
