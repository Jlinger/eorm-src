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
namespace Eorm\Library;

use Eorm\Exceptions\EormException;

class Helper
{
    public static function standardise($value)
    {
        if (is_array($value)) {
            $temp = [];
            foreach ($value as $k => $v) {
                $temp[$k] = '`' . $v . '`';
            }
            return $temp;
        } else {
            return '`' . $value . '`';
        }
    }

    public static function range($id, $length, $regular = false)
    {
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

    public static function merge(array $source, array $target = [])
    {
        if (!empty($source)) {
            foreach ($source as $value) {
                $target[] = $value;
            }
        }

        return $target;
    }

    public static function fill($length, $element = '?', $brackets = true)
    {
        $sequence = implode(',', array_fill(0, $length, $element));
        if ($brackets) {
            return '(' . $sequence . ')';
        } else {
            return $sequence;
        }
    }

    public static function toScalar($value)
    {
        if (is_string($value) || is_numeric($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return (int) $value;
        } elseif (is_null($value)) {
            return '';
        } else {
            throw new EormException('Data can not be converted into a scalar.');
        }
    }

    public static function mergeField(array $field)
    {
        return implode(',', static::standardise($field));
    }

    public static function makeInsertArray(array $source)
    {
        $source = array_map(
            function ($column) {
                return (array) $column;
            },
            $source
        );

        $rows   = max(array_map('count', $source));
        $source = array_map(
            function ($column) use ($rows) {
                return array_pad($column, $rows, end($column));
            },
            $source
        );

        $columns  = count($source);
        $argument = new Argument();
        array_unshift($source, function () use ($argument) {
            $argument->push(func_get_args());
            return true;
        });

        call_user_func_array('array_map', $source);

        return [$argument, $rows, $columns];
    }

    public static function makeWhereWithPrimaryKey($primaryKey, $length)
    {
        if ($length === 1) {
            return static::standardise($primaryKey) . '=?';
        } else {
            return static::standardise($primaryKey) . ' IN ' . static::fill($length);
        }
    }
}
