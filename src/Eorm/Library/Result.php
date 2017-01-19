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

use Closure;
use Eorm\Exceptions\EormException;

/**
 *
 */
class Result
{
    /**
     * [$source description]
     * @var [type]
     */
    protected $source;

    /**
     * [$columns description]
     * @var array
     */
    protected $columns = [];

    /**
     * [__construct description]
     * @param array $source [description]
     */
    public function __construct(array $source)
    {
        $this->source = $source;
        if (!empty($source)) {
            foreach ($source[0] as $key => $value) {
                $this->columns[$key] = $key;
            }
        }
    }

    /**
     * [existsColumnName description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function existsColumnName($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * [checkColumnName description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function checkColumnName($name)
    {
        if (!$this->existsColumnName($name)) {
            throw new EormException('Unknown column: ' . $name);
        }

        return $this;
    }

    /**
     * [standardiseColumnNames description]
     * @param  [type] $names [description]
     * @return [type]        [description]
     */
    protected function standardiseColumnNames($names)
    {
        $standardizedColumns = [];

        if (is_string($names)) {
            $this->checkColumnName($names);
            $standardizedColumns[$names] = $names;
        } elseif (is_array($names)) {
            if (empty($names)) {
                $standardizedColumns = $this->columns;
            } else {
                foreach ($names as $alias => $column) {
                    $this->checkColumnName($column);
                    $standardizedColumns[is_numeric($alias) ? $column : $alias] = $column;
                }
            }
        } elseif (is_callable($names)) {
            foreach ($this->columns as $column) {
                $alias = call_user_func($names, $column);
                if (is_string($alias)) {
                    $standardizedColumns[$alias] = $column;
                } elseif (is_array($alias)) {
                    foreach ($alias as $v) {
                        if (is_string($v)) {
                            $standardizedColumns[$v] = $column;
                        } else {
                            throw new EormException('Invalid column alias name.');
                        }
                    }
                } else {
                    throw new EormException('Invalid column alias name.');
                }
            }
        } else {
            throw new EormException('Invalid column names.');
        }

        return $standardizedColumns;
    }

    /**
     * [isEmpty description]
     * @return boolean [description]
     */
    public function isEmpty()
    {
        return empty($this->source);
    }

    /**
     * [count description]
     * @return [type] [description]
     */
    public function count()
    {
        return count($this->source);
    }

    /**
     * [filter description]
     * @param  Closure $closure [description]
     * @param  [type]  $option  [description]
     * @return [type]           [description]
     */
    public function filter(Closure $closure, $option = null)
    {
        if (!$this->isEmpty()) {
            $filtered = [];
            foreach ($this->source as $row) {
                if ($closure($row, $option)) {
                    $filtered[] = $row;
                }
            }

            $this->source = $filtered;
            if (empty($filtered)) {
                $this->columns = [];
            }
        }

        return $this;
    }

    /**
     * [each description]
     * @param  Closure $closure [description]
     * @param  [type]  $option  [description]
     * @return [type]           [description]
     */
    public function each(Closure $closure, $option = null)
    {
        $calculated = [];

        if (!$this->isEmpty()) {
            foreach ($this->source as $row) {
                $calculated[] = $closure($row, $option);
            }
        }

        return $calculated;
    }

    /**
     * [map description]
     * @param  array  $map       [description]
     * @param  [type] $target    [description]
     * @param  [type] $reference [description]
     * @param  [type] $default   [description]
     * @return [type]            [description]
     */
    public function map(array $map, $target, $reference = null, $default = null)
    {
        if (is_null($reference)) {
            $reference = $target;
        }

        $this->checkColumnName($reference);

        $this->source = array_map(
            function ($row) use ($map, $target, $reference, $default) {
                $assignment = $row[$reference];
                if (array_key_exists($assignment, $map)) {
                    $row[$target] = $map[$assignment];
                } else {
                    $row[$target] = $default;
                }
                return $row;
            },
            $this->source
        );

        return $this;
    }

    /**
     * [group description]
     * @param  [type] $group [description]
     * @param  array  $names [description]
     * @return [type]        [description]
     */
    public function group($group, $names = [])
    {
        $groupData = [];

        if (!$this->isEmpty()) {
            $this->checkColumnName($group);
            $standardizedNames = $this->standardiseColumnNames($names);
            if (is_string($names)) {
                $column = reset($standardizedNames);
                foreach ($this->source as $row) {
                    if (isset($groupData[$row[$group]])) {
                        $groupData[$row[$group]][] = $row[$column];
                    } else {
                        $groupData[$row[$group]] = [$row[$column]];
                    }
                }
            } else {
                foreach ($this->source as $row) {
                    $calculatedRow = [];
                    foreach ($standardizedNames as $alias => $column) {
                        $calculatedRow[$alias] = $row[$column];
                    }

                    if (isset($groupData[$row[$group]])) {
                        $groupData[$row[$group]][] = $calculatedRow;
                    } else {
                        $groupData[$row[$group]] = [$calculatedRow];
                    }
                }
            }
        }

        return $groupData;
    }

    /**
     * [transpose description]
     * @return [type] [description]
     */
    public function transpose()
    {
        $transposeData = [];
        if (!$this->isEmpty()) {
            foreach ($this->source as $row) {
                foreach ($row as $column => $value) {
                    if (isset($transposeData[$column])) {
                        $transposeData[$column][] = $value;
                    } else {
                        $transposeData[$column] = [$value];
                    }
                }
            }
        }

        return $transposeData;
    }

    /**
     * [transform description]
     * @param  array  $map [description]
     * @return [type]      [description]
     */
    public function transform(array $map)
    {
        if ($this->isEmpty() || empty($map)) {
            return $this;
        }

        $this->source = array_map(
            function ($row) use ($map) {
                foreach ($map as $column => $type) {
                    $this->checkColumnName($column);
                    $type = strtolower(trim($type));
                    if ($type === 'integer') {
                        $row[$column] = (int) $row[$column];
                    } elseif ($type === 'boolean') {
                        $row[$column] = (bool) $row[$column];
                    } elseif ($type === 'float') {
                        $row[$column] = (float) $row[$column];
                    } elseif ($type === 'array') {
                        $row[$column] = [$row[$column]];
                    } else {
                        throw new EormException(
                            'Invalid transform type: ' . $type
                        );
                    }
                }
                return $row;
            },
            $this->source
        );

        return $this;
    }

    /**
     * [row description]
     * @param  [type] $index [description]
     * @return [type]        [description]
     */
    public function row($index)
    {
        if ($this->isEmpty()) {
            return [];
        } else {
            if ($index < 0) {
                $index = $index + $this->count();
            }

            if (isset($this->source[$index])) {
                return $this->source[$index];
            } else {
                throw new EormException('Unknown row: ' . $index);
            }
        }
    }

    /**
     * [first description]
     * @return [type] [description]
     */
    public function first()
    {
        if ($this->isEmpty()) {
            return [];
        } else {
            return reset($this->source);
        }
    }

    public function last()
    {
        if ($this->isEmpty()) {
            return [];
        } else {
            return end($this->source);
        }
    }

    /**
     * [column description]
     * @param  [type]  $name   [description]
     * @param  boolean $unique [description]
     * @return [type]          [description]
     */
    public function column($name, $unique = false)
    {
        if ($this->isEmpty()) {
            return [];
        } else {
            $this->checkColumnName($name);
            if ($unique) {
                return array_unique(array_column($this->source, $name));
            } else {
                return array_column($this->source, $name);
            }
        }
    }

    /**
     * [toArray description]
     * @param  array  $columns [description]
     * @return [type]          [description]
     */
    public function toArray($columns = [])
    {
        if (empty($columns) || $this->isEmpty()) {
            return $this->source;
        }

        $standardizedNames = $this->standardiseColumnNames($columns);
        return array_map(
            function ($row) use ($standardizedNames) {
                $rowData = [];
                foreach ($standardizedNames as $alias => $column) {
                    $rowData[$alias] = $row[$column];
                }
                return $rowData;
            },
            $this->source
        );
    }

    /**
     * [__get description]
     * @param  [type] $column [description]
     * @return [type]         [description]
     */
    public function __get($column)
    {
        if ($this->existsColumnName($column)) {
            $data = array_column($this->source, $column);
            if (count($data) > 1) {
                return $data;
            } else {
                return reset($data);
            }
        } else {
            return null;
        }
    }

    /**
     * [__isset description]
     * @param  [type]  $column [description]
     * @return boolean         [description]
     */
    public function __isset($column)
    {
        return $this->existsColumnName($column);
    }
}
