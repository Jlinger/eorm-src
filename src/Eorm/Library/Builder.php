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

/**
 * Edoger ORM SQL Component Builder Class.
 */
final class Builder
{
    /**
     * Create the SQL statement field part.
     *
     * @param  string|array  $field  The field name(s).
     * @return string
     */
    public static function makeField($field)
    {
        if (is_array($field)) {
            return implode(',', Helper::formatArray($field));
        } else {
            return Helper::format($field);
        }
    }

    /**
     * [makeCountField description]
     * @param  [type] $field    [description]
     * @param  [type] $distinct [description]
     * @return [type]           [description]
     */
    public static function makeCountField($field, $distinct)
    {
        $column = Helper::format($field);

        if ($distinct) {
            return "COUNT(DISTINCT {$column}) AS `total`";
        } else {
            return "COUNT({$column}) AS `total`";
        }
    }

    /**
     * [normalizeInsertRows description]
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
    public static function normalizeInsertRows(array $columns)
    {
        $columns = array_map([Helper::class, 'toArray'], $columns);
        $maximum = max(array_map('count', $columns));
        if ($maximum === 1) {
            return $columns;
        } else {
            return array_map(
                function ($column) use ($maximum) {
                    return array_pad($column, $maximum, end($column));
                },
                $columns
            );
        }
    }

    /**
     * Create the SQL statement where part.
     *
     * @param  string   $field  The field name.
     * @param  integer  $count  The field value count number.
     * @return string
     */
    public static function makeWhereIn($field, $count)
    {
        if ($count > 1) {
            return Helper::format($field) . ' IN ' . Helper::fill($count);
        } else {
            return Helper::format($field) . '=?';
        }
    }

}
