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
namespace Eorm\Builder;

use Eorm\Foundation\Actuator;
use Eorm\Foundation\Parameter;

/**
 *
 */
abstract class Basic
{
    /**
     * [$parameter description]
     *
     * @var Eorm\Foundation\Parameter
     */
    private $parameter;

    /**
     * [$actuator description]
     *
     * @var Eorm\Foundation\Actuator
     */
    private $actuator;

    /**
     * [$type description]
     * @var null
     */
    protected static $type = null;

    /**
     * [__construct description]
     *
     * @param Eorm\Foundation\Actuator   $actuator   [description]
     * @param Eorm\Foundation\Parameter  $parameter  [description]
     */
    public function __construct(Actuator $actuator, Parameter $parameter)
    {
        $this->actuator  = $actuator;
        $this->parameter = $parameter;
    }

    /**
     * [parameter description]
     *
     * @return Eorm\Foundation\Parameter
     */
    final public function parameter()
    {
        return $this->parameter;
    }

    /**
     * [actuator description]
     *
     * @return Eorm\Foundation\Actuator
     */
    final public function actuator()
    {
        return $this->actuator;
    }

    /**
     * [type description]
     *
     * @return string
     */
    final public function type()
    {
        return static::$type;
    }

    /**
     * [build description]
     *
     * @return string
     */
    abstract public function build();
}
