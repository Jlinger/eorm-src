<?php
/**
 *+------------------------------------------------------------------------------------------------+
 *| Edoger ORM                                                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| A Simple PHP Object Relational Mapping Library.                                                |
 *+------------------------------------------------------------------------------------------------+
 *| @license   MIT                                                                                 |
 *| @link      https://www.edoger.com/                                                             |
 *| @copyright Copyright (c) 2016 Qingshan Luo                                                     |
 *+------------------------------------------------------------------------------------------------+
 *| @author    Qingshan Luo <shanshan.lqs@gmail.com>                                               |
 *+------------------------------------------------------------------------------------------------+
 */
namespace Eorm;

use Closure;
use Eorm\Exceptions\EormException;
use Exception;
use Throwable;

class Event
{
    protected static $executeEvents = [];

    public static function onExecute(Closure $handler)
    {
        return array_push(static::$executeEvents, $handler);
    }

    public static function triggerExecute($sql, array $arguments = [])
    {
        if (!empty(static::$executeEvents)) {
            try {
                foreach (static::$executeEvents as $handler) {
                    $handler($sql, $arguments);
                }
            } catch (Exception $e) {
                throw new EormException('Event handler error: ' . $e->getMessage());
            } catch (Throwable $e) {
                throw new EormException('Event handler error: ' . $e->getMessage());
            }
        }
    }
}
