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

use Eorm\Eorm;
use Eorm\Exceptions\EormException;

/**
 * Eorm builder basic class.
 *
 * All SQL statement builders must inherit this basic class.
 * We will manage and inject the dependencies of the SQL statement builder.
 */
abstract class Basic
{
    /**
     * The database table name.
     *
     * @var string
     */
    private $table;

    /**
     * The database table primary key field name.
     *
     * @var string
     */
    private $primaryKey;

    /**
     * The database server connection name.
     *
     * @var string
     */
    private $server;

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
     * @param  string     $table       The database table name.
     * @param  string     $primaryKey  The database table primary key field name.
     * @param  string     $server      The database server connection name.
     * @param  Parameter  $parameter   The SQL statement parameter manager instance.
     * @return void
     */
    public function __construct($table, $primaryKey, $server, Parameter $parameter)
    {
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
        $this->server     = $server;
        $this->parameter  = $parameter;
    }

    /**
     * Gets database table name.
     *
     * @return string
     */
    final public function getTable()
    {
        return $this->table;
    }

    /**
     * Gets database table primary key field name.
     *
     * @return string
     */
    final public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Gets database server connection name.
     *
     * @return string
     */
    final public function getServer()
    {
        return $this->server;
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
     * Format table name or field name.
     *
     * @param  string  $name  The table name or field name.
     * @return string
     */
    protected function format($name)
    {
        if (is_string($name)) {
            return '`' . str_replace('`', '``', $name) . '`';
        } else {
            throw new EormException(
                'Table name or field name must be a string.',
                Eorm::ERROR_ARGUMENT
            );
        }
    }

    /**
     * Format multiple table names or field names.
     *
     * @param  array  $names  The table names or field names.
     * @return array
     */
    protected function formatArray(array $names)
    {
        return array_map([$this, 'format'], $names);
    }

    /**
     * Gets the formatted database table name.
     *
     * @return string
     */
    protected function formatTable()
    {
        return $this->format($this->getTable());
    }

    /**
     * Gets the formatted database table primary key field name
     *
     * @return string
     */
    protected function formatPrimaryKey()
    {
        return $this->format($this->getPrimaryKey());
    }

    /**
     * Use commas to connect multiple strings.
     *
     * @param  array  $values  The string array.
     * @return string
     */
    protected function join(array $values)
    {
        return implode(',', $values);
    }

    /**
     * Build SQL statement.
     *
     * @return string
     */
    abstract public function build();
}
