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

require __DIR__ . '/../vendor/autoload.php';

// Add MySQL server connection.
Eorm\Server::add(
    function () {
        return new PDO('mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8', 'root', '');
    },
    'eorm'
);

// Load test model.
require __DIR__ . '/../src/Models/Example.php';
