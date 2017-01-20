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

require __DIR__ . '/../vendor/autoload.php';

// Add MySQL Server Connection To Eorm\Server Class.
// Default server name is 'eorm'.
// Use Table 'example'.
if (file_exists(__DIR__ . '/../test.lock')) {
    Eorm\Server::add(
        function () {
            return new PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8',
                'eorm',
                'eorm'
            );
        },
        'eorm'
    );
} else {
    Eorm\Server::add(
        function () {
            return new PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8',
                'root',
                ''
            );
        },
        'eorm'
    );
}

// Load Example Model Class.
require __DIR__ . '/../src/Models/Example.php';
