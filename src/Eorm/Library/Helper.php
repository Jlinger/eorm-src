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
namespace Eorm\Library;

use Eorm\Exceptions\EormException;

/**
 *
 */
class Helper
{
    /**
     * [format description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function format($value)
    {
        if (is_string($value)) {
            return '`' . str_replace('`', '``', $value) . '`';
        }

        throw new EormException('Column name must be a string.');
    }

    /**
     * [formatArray description]
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    public static function formatArray(array $values)
    {
        return array_map('self::format', $values);
    }

    /**
     * [toArray description]
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    public static function toArray($values)
    {
        if (is_array($values)) {
            if (empty($values)) {
                return $values;
            } else {
                return array_values($values);
            }
        } else {
            return [$values];
        }
    }

    /**
     * [join description]
     * @param  array  $elements [description]
     * @return [type]           [description]
     */
    public static function join(array $elements)
    {
        return implode(',', $elements);
    }

    /**
     * [range description]
     * @param  [type]  $id      [description]
     * @param  [type]  $length  [description]
     * @param  boolean $regular [description]
     * @return [type]           [description]
     */
    public static function range($id, $length, $regular = false)
    {
        if (!is_int($id)) {
            $id = intval($id);
        }

        if ($length <= 1) {
            if ($regular) {
                return [$id];
            } else {
                return $id;
            }
        } else {
            return range($id, $id + $length - 1);
        }
    }

    /**
     * [fill description]
     * @param  [type]  $length   [description]
     * @param  string  $element  [description]
     * @param  boolean $brackets [description]
     * @return [type]            [description]
     */
    public static function fill($length, $element = '?', $brackets = true)
    {
        $sequence = static::join(array_fill(0, $length, $element));

        if ($brackets) {
            return '(' . $sequence . ')';
        } else {
            return $sequence;
        }
    }

    /**
     * [toScalar description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function toScalar($value)
    {
        if (is_string($value) || is_numeric($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return $value ? 1 : 0;
        } elseif (is_null($value)) {
            return '';
        } else {
            throw new EormException('Data can not be converted into a scalar.');
        }
    }
}
