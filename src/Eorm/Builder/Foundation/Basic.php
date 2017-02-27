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
namespace Eorm\Builder\Foundation;

use Eorm\Foundation\Executor;

/**
 * Eorm builder basic class.
 *
 * All SQL statement builders must inherit this basic class.
 * We will manage and inject the dependencies of the SQL statement builder.
 */
abstract class Basic
{
    /**
     * The SQL statement executor instance.
     *
     * @var Executor
     */
    private $executor;

    /**
     * The SQL statement parameter manager instance.
     *
     * @var Parameter
     */
    private $parameter;

    /**
     * The SQL statement builder type name.
     * All SQL statement builders must override this property.
     * This property is used to guide the event trigger to execute the corresponding event handler.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Initializes the SQL statement builder instance,
     * and injecting the necessary dependency resources.
     *
     * @param  Executor   $executor   The SQL statement executor instance.
     * @param  Parameter  $parameter  The SQL statement parameter manager instance.
     * @return void
     */
    public function __construct(Executor $executor, Parameter $parameter = null)
    {
        $this->executor = $executor;
        if ($parameter) {
            $this->parameter = $parameter;
        } else {
            $this->parameter = new Parameter();
        }
    }

    /**
     * Gets SQL statement executor instance.
     *
     * @return Executor
     */
    final public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * Gets SQL statement parameter manager instance.
     *
     * @return Parameter
     */
    final public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Gets SQL statement builder type name.
     *
     * @return string
     */
    final public function getType()
    {
        return $this->type;
    }

    /**
     * Build SQL statement.
     *
     * @return string
     */
    abstract public function build();
}
