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
namespace Eorm\Exceptions;

use Eorm\Contracts\Exceptions\ConfigurationExceptionInterface;

/**
 * Configuration exception class.
 */
class ConfigurationException extends EormException implements ConfigurationExceptionInterface
{
    /**
     * The exception model class name.
     *
     * @var string
     */
    protected $model;

    /**
     * The exception model configuration item name.
     *
     * @var string
     */
    protected $configurationItem;

    /**
     * Construct the configuration exception.
     *
     * @param string   $message            The exception message.
     * @param integer  $code               The exception code.
     * @param string   $model              The exception model class name.
     * @param string   $configurationItem  The exception model configuration item name.
     */
    public function __construct($message, $code, $model, $configurationItem)
    {
        $this->model             = $model;
        $this->configurationItem = $configurationItem;

        parent::__construct($message, $code);
    }

    /**
     * Gets the Exception model class name.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Gets the Exception model configuration item name.
     *
     * @return string
     */
    public function getConfigurationItem()
    {
        return $this->configurationItem;
    }
}
