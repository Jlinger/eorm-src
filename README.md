# About #

- Eorm is a simple PHP object relational mapping component.
- Eorm can help the application to easily operate the MySQL.
- Eorm can be easily integrated into any application.

# Documentation #

### Bind connection ###

```php
<?php
    $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8';
    $username = 'eorm';
    $password = 'eorm';

    // Bind connect to Eorm\Server.
    Eorm\Server::bind(new PDO($dsn, $username, $password));
?>
```

### Create model ###

```php
<?php
class Users extends Eorm\Eorm
{
    // The default table name is the model class name (users).
    // This is an optional attribute.
    protected static $table = null;

    // The default auto increment primary key is 'id'.
    // This is an optional attribute.
    protected static $primaryKey = 'id';
}
?>
```

### Create table ###

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | Xiaoming   | 18         | China      |

### Common methods ###

Query by primary key.

```php
<?php
    // ['id' => '1', 'name' => 'Xiaoming', 'age' => '18', 'country' => 'China']
    Users::find(1)->result()->toArray()[0];

    // ['name' => 'Xiaoming', 'country' => 'China']
    Users::find(1)->result()->toArray(['name', 'country'])[0];
?>
```

Insert row.

```php
<?php
    // Insert 1 row.
    Users::create([
        'name'    => 'Dahuang',
        'age'     => 3,
        'country' => 'China',
    ]);

    // Insert 2 rows.
    Users::create([
        'name'    => ['Tom', 'Aik'],
        'age'     => [24, 22],
        'country' => 'America', // The same value can be shortened.
    ]);
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | Xiaoming   | 18         | China      |
| 2          | Dahuang    | 3          | China      |
| 3          | Tom        | 24         | America    |
| 4          | Aik        | 22         | America    |

Query all.

```php
<?php
    // [
    //   ['id' => '1', 'name' => 'Xiaoming', 'age' => '18', 'country' => 'China'],
    //   ['id' => '2', 'name' => 'Dahuang',  'age' => '3',  'country' => 'China'],
    //   ['id' => '3', 'name' => 'Tom',      'age' => '24', 'country' => 'America'],
    //   ['id' => '4', 'name' => 'Aik',      'age' => '22', 'country' => 'America']
    // ]
    Users::all()->result()->toArray();
?>
```

Query count.

```php
<?php
    // 4
    Users::count();
?>
```

Where.

```php
<?php
    // ['id' => '2', 'name' => 'Dahuang',  'age' => '3',  'country' => 'China']
    Users::where('name', 'Dahuang')->get()->result()->toArray()[0];

    // [
    //   ['id' => '3', 'name' => 'Tom', 'age' => '24', 'country' => 'America'],
    //   ['id' => '4', 'name' => 'Aik', 'age' => '22', 'country' => 'America']
    // ]
    Users::where('age', 20, '>')->get()->result()->toArray();
    // or
    Users::where('age', [24, 22])->get()->result()->toArray();

    // ['id' => '4', 'name' => 'Aik', 'age' => '22', 'country' => 'America']
    Users::where('age', 20, '>')->where('age', 23, '<')->get()->result()->toArray()[0];
?>
```

Delete.

```php
<?php
    // Delete row of 'id' equal 1.
    Users::destroy(1);
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 2          | Dahuang    | 3          | China      |
| 3          | Tom        | 24         | America    |
| 4          | Aik        | 22         | America    |


```php
<?php
    // Delete row of 'name' equal 'Tom'.
    Users::where('name', 'Tom')->get()->delete();
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 2          | Dahuang    | 3          | China      |
| 4          | Aik        | 22         | America    |


Clean all data.

```php
<?php
    // The table is empty.
    Users::clean();
?>
```

Update row.

```php
<?php
    // Insert 1 row.
    Users::create([
        'name'    => 'Dahuang',
        'age'     => 3,
        'country' => 'China',
    ]);
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | Dahuang    | 3          | China      |


```php
<?php
    // Update 'age' to 5.
    Users::find(1)->set('age', 5)->save();
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | Dahuang    | 5          | China      |

Replace row.

```php
<?php
    // Delete row and insert row.
    // The 'id' equal 2.
    Users::find(1)->set('age', 7)->replace();
?>
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 2          | Dahuang    | 7          | China      |