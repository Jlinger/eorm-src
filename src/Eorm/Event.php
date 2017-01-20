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

use Eorm\Contracts\EventInterface;
use Eorm\Contracts\Event\EventBodyInterface;
use Eorm\Contracts\Event\EventHandlerInterface;
use Eorm\Exceptions\EventException;
use Exception;
use Throwable;

/**
 * Eorm event manager class.
 */
class Event implements EventInterface
{
    /**
     * All registered Eorm event handlers.
     *
     * @var array
     */
    private static $eventHandlers = [
        'select'  => [],
        'update'  => [],
        'insert'  => [],
        'delete'  => [],
        'replace' => [],
        'clean'   => [],
        'count'   => [],
        'execute' => [],
    ];

    /**
     * Register a Eorm event handler.
     *
     * @param  string                 $name     The Eorm event name.
     * @param  EventHandlerInterface  $handler  The Eorm event handler.
     * @return integer
     */
    public static function on($name, EventHandlerInterface $handler)
    {
        self::check($name);

        return array_unshift($this->eventHandlers[$name], $handler);
    }

    /**
     * Delete a Eorm event handler.
     *
     * @param  string   $name  The Eorm event name.
     * @param  boolean  $all   Delete all event handlers ? (no)
     * @return EventHandlerInterface|array|null
     */
    public static function off($name, $all = false)
    {
        self::check($name);

        if (empty($this->eventHandlers[$name])) {
            return null;
        } else {
            if ($all) {
                $handlers = $this->eventHandlers[$name];

                $this->eventHandlers[$name] = [];
                return $handlers;
            } else {
                return array_shift($this->eventHandlers[$name]);
            }
        }
    }

    /**
     * Trigger a Eorm event.
     *
     * @param  EventBodyInterface  $body  The Eorm event body instanse.
     * @return boolean
     */
    public static function trigger(EventBodyInterface $body)
    {
        $name = $body->name();

        self::check($name);
        if (empty($this->eventHandlers[$name])) {
            return true;
        }

        try {
            foreach ($this->eventHandlers[$name] as $handler) {
                $state = $handler->handle($body);
                if (!is_bool($state)) {
                    throw new EventException(
                        "The event handler must return a Boolean value.",
                        Eorm::ERROR_EVENT,
                        $name
                    );
                }

                if ($state === false) {
                    return $state;
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

    /**
     * Check whether a Eorm event has a handler.
     * For an invalid Eorm event name, the method returns false.
     *
     * @param  string  $name  The Eorm event name.
     * @return boolean
     */
    public static function exists($name)
    {
        return !empty(self::$eventHandlers[$name]);
    }

    /**
     * Check whether a Eorm event name is valid.
     * If the Eorm event name is invalid, an 'EventException' exception is thrown.
     *
     * @param  string  $name  The Eorm event name.
     * @return void
     */
    private static function check($name)
    {
        if (!isset($this->eventHandlers[$name])) {
            throw new EventException(
                "Invalid event name '{$name}'.",
                Eorm::ERROR_EVENT,
                'event'
            );
        }
    }
}
