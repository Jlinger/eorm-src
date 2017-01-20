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
namespace Eorm\Contracts\Exceptions;

/**
 * Configuration exception class interface.
 */
interface ConfigurationExceptionInterface
{
    /**
     * Gets the Exception model class name.
     *
     * @return string
     */
    public function getModel();

    /**
     * Gets the Exception model configuration item name.
     *
     * @return string
     */
    public function getConfigurationItem();
}
