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

class Argument
{
    protected $arguments = [];

    public function __construct(array $arguments = [])
    {
        if (!empty($arguments)) {
            foreach ($arguments as $value) {
                $this->arguments[] = Helper::toScalar($value);
            }
        }
    }

    public function push($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->arguments[] = Helper::toScalar($v);
            }
        } else {
            $this->arguments[] = Helper::toScalar($value);
        }

        return $this;
    }

    public function count()
    {
        return count($this->arguments);
    }

    public function toArray()
    {
        return $this->arguments;
    }
}
