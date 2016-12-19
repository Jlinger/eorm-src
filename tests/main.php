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

spl_autoload_register(function ($abstract) {
    require dirname(__DIR__) . '/src/' . str_replace('\\', '/', trim($abstract, '\\')) . '.php';
});

call_user_func(function () {
    $host     = '127.0.0.1';
    $port     = 3306;
    $database = 'eorm';
    $charset  = 'utf8';

    Eorm\Server::bind(
        new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset={$charset}", 'eorm', 'eorm'
        )
    );

    Eorm\Event::onExecute(function ($sql, $argument) {
        echo '[' . $sql . '][' . implode(',', $argument) . ']' . PHP_EOL;
    });
});

class Users extends Eorm\Eorm
{
    // Test model.
}

Users::clean();
Users::getTable();
Users::getPrimaryKey();
Users::create([
    'user_name'    => 'Test',
    'user_age'     => 20,
    'user_address' => 'ShangHai',
]);
Users::create([
    'user_name'    => ['Test1', 'Test2', 'Test3'],
    'user_age'     => 15,
    'user_address' => ['BeiJing', 'HangZhou'],
]);
Users::all();
Users::count();
Users::find(1);
Users::find([1, 3]);
Users::destroy(1);
Users::destroy([2, 3, 4]);
Users::create([
    'user_name'    => ['Test1', 'Test2', 'Test3', 'Test4', 'Test5'],
    'user_age'     => [10, 20, 30, 40, 50],
    'user_address' => ['ShangHai', 'BeiJing', 'HangZhou', 'TianJing', 'GuangZhou'],
]);
Users::query()->get();
Users::transaction(function () {
    Users::create([
        'user_name'    => 'Test',
        'user_age'     => 5,
        'user_address' => 'ShangHai',
    ]);
    Users::find(10);
    Users::destroy(10);
});
Users::where('id', 5)->get();
Users::where('id', [5, 6, 7])->get();
Users::where('id', 5, '>')->get();
Users::where('id', 6, '<=')->get();
Users::where(function ($where) {
    $where->compare('id', 7);
})->get();
Users::where(function ($where) {
    $where->compare('id', [7, 8, 9]);
})->get();
Users::where(function ($where) {
    $where->compare('user_age', 20, '>=')->compare('user_age', 40, '<');
})->get();
Users::where(function ($where) {
    $where->compare('id', 7)->compare('id', 8);
}, false)->get();
Users::where(function ($where) {
    $where->like('user_address', '%Zhou');
})->get();
Users::where(function ($where) {
    $where->compare('id', 5, '>=')->group(function ($where) {
        $where->like('user_address', '%Zhou')->like('user_address', '%Jing');
    });
})->get();
Users::query()->limit(3)->get();
Users::query()->limit(3)->skip(1)->get();
Users::query()->limit(2)->skip(2)->get();
Users::where('id', [6, 7, 8, 9])->limit(2)->skip(1)->get();

Users::find(5)->set('user_age', 100)->save();
Users::all()->set('user_age', 150)->set('user_address', 'ChongQing')->save();
Users::all()->delete();
Users::all()->create([
    'user_name'    => ['Test1', 'Test2'],
    'user_age'     => 20,
    'user_address' => 'ShangHai',
]);
Users::all()->set('user_age', 50)->replace();
Users::destroy(Users::all()->result()->id);

// ----------------------------------------------------------
Users::clean();
Users::create([
    'user_name'    => ['Test1', 'Test2', 'Test3', 'Test4', 'Test5'],
    'user_age'     => [10, 20, 30, 40, 50],
    'user_address' => ['ShangHai', 'BeiJing', 'user_address', 'TianJing', 'GuangZhou'],
]);
Users::find(Users::all()->result()->id)
    ->create([
        'user_name'    => ['Test6', 'Test7'],
        'user_age'     => 80,
        'user_address' => 'ShangHai',
    ])
    ->set('user_age', 100)
    ->replace()
    ->delete()
    ->set('user_name', 'Test')
    ->set('user_age', 10)
    ->set('user_address', 'user_address')
    ->save()
    ->reload()
    ->set('user_age', 20)
    ->replace()
    ->delete();

// ----------------------------------------------------------
Users::clean();
Users::create([
    'user_name'    => ['Test1', 'Test2', 'Test3', 'Test4', 'Test5'],
    'user_age'     => [10, 10, 20, 20, 30],
    'user_address' => ['ShangHai', 'BeiJing', 'user_address', 'TianJing', 'GuangZhou'],
]);
Users::all()->result()->isEmpty();
Users::all()->result()->count();
Users::all()->result()->filter(function ($row) {
    return in_array(
        $row['id'],
        Users::where('user_age', 20)->get()->result()->id
    );
});
Users::all()->result()->each(function ($row) {
    return implode($row);
});
Users::all()->result()->map([
    10 => 'S',
    30 => 'L',
], 'size', 'user_age', 'U');
Users::all()->result()->group('user_age', ['id', 'user_age']);
Users::all()->result()->transpose();
Users::all()->result()->transform(['user_age' => 'integer']);
Users::all()->result()->transform(['user_age' => 'float']);
Users::all()->result()->transform(['user_age' => 'array']);
Users::all()->result()->row(2);
Users::all()->result()->row(-2);
Users::query()->one()->result()->first();
Users::query()->one()->result()->last();
Users::all()->result()->column('user_age');
Users::all()->result()->column('user_age', true);
Users::all()->result()->toArray();
Users::all()->result()->toArray([
    'age'  => 'user_age',
    'age2' => 'user_age',
]);

Users::where('user_age', 30, '>=')->count();
Users::where('user_age', 30)->exists();
Users::all()->delete();
