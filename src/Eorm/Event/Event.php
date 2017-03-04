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

use Eorm\Eorm;
use Eorm\Exceptions\EormException;
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
        'execute' => [],
        'select'  => [],
        'update'  => [],
        'insert'  => [],
        'delete'  => [],
        'clean'   => [],
        'count'   => [],
        'exist'   => [],
        'replace' => [],
    ];

    /**
     * Check whether a Eorm event has a handler.
     * For an invalid event name, return false.
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
     * Delete all Eorm event handler by event name.
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
     * Trigger a Eorm event handler.
     *
     * @param  Body  $body  The Eorm event body instanse.
     * @return boolean
     */
    public function trigger(Body $body)
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
        } catch (EormException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new EormException($e->getMessage(), Eorm::ERROR_EVENT);
        } catch (Throwable $e) {
            throw new EormException($e->getMessage(), Eorm::ERROR_EVENT);
        }

        return false;
    }
}
