# About Eorm #

- Eorm is a simple PHP object relational mapping library.
- Eorm is very simple, you don't have to spend too much time learning it.
- Eorm can help the application to easily operate the MySQL.
- Eorm can be easily integrated into any application.

# Example #

1. Create a database connection.

```php
$username = 'eorm';
$password = 'eorm';
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8';

$connect = new PDO($dsn, $username, $password);
```

2. Bind the connection to Eorm\Server.

```php
Eorm\Server::bind(new PDO($dsn, $username, $password));

```

3. Create a table model.

```php
class Users extends Eorm\Eorm
{
    // The default table name is the model class name (users).
    // This is an optional attribute.
    protected static $table = null;

    // The default auto increment primary key is 'id'.
    // This is an optional attribute.
    protected static $primaryKey = 'id';
}
```

4. Create database table.

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | XiaoMing   | 18         | China      |
| 2          | DaHuang    | 10         | China      |

5. Query by primary key.

```php
// [
//   ['id' => '1', 'name' => 'XiaoMing', 'age' => '18', 'country' => 'China']
// ]
Users::find(1)->result()->toArray();
```

6. Query all.

```php
// [
//   ['id' => '1', 'name' => 'XiaoMing', 'age' => '18', 'country' => 'China'],
//   ['id' => '2', 'name' => 'Dahuang',  'age' => '10', 'country' => 'China'],
// ]
Users::all()->result()->toArray();
```

7. Query count.

```php
Users::count();  // 2
```

8. Query by condition.

```php
// [
//   ['id' => '2', 'name' => 'Dahuang',  'age' => '10', 'country' => 'China'],
// ]
Users::where('name', 'Dahuang')->get()->result()->toArray();

// [
//   ['id' => '1', 'name' => 'XiaoMing', 'age' => '18', 'country' => 'China']
// ]
Users::where('age', 15, '>')->get()->result()->toArray();
```

9. Insert row.

```php
Users::create(['name' => 'Halle', 'age' => 22, 'country' => 'America']);
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | XiaoMing   | 18         | China      |
| 2          | DaHuang    | 10         | China      |
| 3          | Halle      | 22         | America    |

10. Insert 3 rows.

```php
Users::create([
    'name'    => ['David', 'Pierre', 'Alice'],
    'age'     => [30, 15, 9],
    'country' => ['America', 'England', 'England']
]);
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | XiaoMing   | 18         | China      |
| 2          | DaHuang    | 10         | China      |
| 3          | Halle      | 22         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 15         | England    |
| 6          | Alice      | 9          | England    |

11. Delete row by primary key.

```php
Users::destroy(1);
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 2          | DaHuang    | 10         | China      |
| 3          | Halle      | 22         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 15         | England    |
| 6          | Alice      | 9          | England    |

11. Delete row by condition.

```php
Users::where('age', 10, '<=')->get()->delete();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 22         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 15         | England    |

12. Update rows.

```php
Users::find([3, 5])->set('age', 20)->save();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 20         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 20         | England    |

13. Replace row.

```php
Users::find([4, 5])->set('age', 45)->replace();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 20         | America    |
| 6          | David      | 45         | America    |
| 7          | Pierre     | 45         | England    |

14. Check exists rows ?

```php
Users::where('country', 'China')->exists();    // false
Users::where('country', 'England')->exists();  // true
```

15. Clean all rows.

```php
Users::clean();  // The table is empty.
```
