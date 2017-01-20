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

use Eorm\Contracts\Exceptions\ConnectExceptionInterface;

/**
 * Database server connect exception class.
 */
class ConnectException extends EormException implements ConnectExceptionInterface
{
    /**
     * The exception database server name.
     *
     * @var string
     */
    protected $server;

    /**
     * Construct the database server connection exception.
     *
     * @param string   $message  The exception message.
     * @param integer  $code     The exception code.
     * @param string   $server   The exception database server name.
     */
    public function __construct($message, $code, $server)
    {
        $this->server = $server;

        parent::__construct($message, $code);
    }

    /**
     * Gets the Exception database server name.
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }
}
