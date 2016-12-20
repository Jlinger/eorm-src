# 介绍 #

- Eorm 是一个轻量级的ORM库。
- Eorm 非常简单，学习成本非常低。
- Eorm 能够让应用程序便捷地操作MySQL数据库。
- Eorm 易于集成，能够快速地应用于你的应用程序中。
- [English](README.md)


[![Build Status](https://travis-ci.org/edoger/eorm-src.svg?branch=master)](https://travis-ci.org/edoger/eorm-src)
[![Latest Stable Version](https://poser.pugx.org/edoger/eorm-src/v/stable)](https://packagist.org/packages/edoger/eorm-src)
[![Latest Unstable Version](https://poser.pugx.org/edoger/eorm-src/v/unstable)](https://packagist.org/packages/edoger/eorm-src)
[![Total Downloads](https://poser.pugx.org/edoger/eorm-src/downloads)](https://packagist.org/packages/edoger/eorm-src)
[![License](https://poser.pugx.org/edoger/eorm-src/license)](https://packagist.org/packages/edoger/eorm-src)

# 示例 #

创建一个到数据库的连接。

```php
$username = 'eorm';
$password = 'eorm';
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=eorm;charset=utf8';

$connect = new PDO($dsn, $username, $password);
```

绑定数据库连接到 Eorm\Server 类。

```php
Eorm\Server::bind($connect);

```

创建表的模型。

```php
class Users extends Eorm\Eorm
{
    // 默认的表名就是是类的名称的小写（users）。
    // 这是一个可选的属性。
    protected static $table = null;

    // 默认的自增主键是 id 。
    // 这是一个可选的属性。
    protected static $primaryKey = 'id';
}
```

创建一个测试表。

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | XiaoMing   | 18         | China      |
| 2          | DaHuang    | 10         | China      |

通过主键查询行。

```php
// [
//   ['id' => '1', 'name' => 'XiaoMing', 'age' => '18', 'country' => 'China']
// ]
Users::find(1)->result()->toArray();
```

查询全表。

```php
// [
//   ['id' => '1', 'name' => 'XiaoMing', 'age' => '18', 'country' => 'China'],
//   ['id' => '2', 'name' => 'Dahuang',  'age' => '10', 'country' => 'China'],
// ]
Users::all()->result()->toArray();
```

查询表的总行数。

```php
Users::count();  // 2
```

通过条件进行查询。

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

插入行。

```php
Users::create(['name' => 'Halle', 'age' => 22, 'country' => 'America']);
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 1          | XiaoMing   | 18         | China      |
| 2          | DaHuang    | 10         | China      |
| 3          | Halle      | 22         | America    |

插入 3 行。

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

通过主键删除行。

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

通过条件删除行。

```php
Users::where('age', 10, '<=')->get()->delete();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 22         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 15         | England    |

更新行。

```php
Users::find([3, 5])->set('age', 20)->save();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 20         | America    |
| 4          | David      | 30         | America    |
| 5          | Pierre     | 20         | England    |

替换行。（这个会执行先删除再插入）

```php
Users::find([4, 5])->set('age', 45)->replace();
```

| id         | name       | age        | country    |
| ---------- | ---------- | ---------- | ---------- |
| 3          | Halle      | 20         | America    |
| 6          | David      | 45         | America    |
| 7          | Pierre     | 45         | England    |

检查行是否存在。

```php
Users::where('country', 'China')->exists();    // false
Users::where('country', 'England')->exists();  // true
```

清空表。（同时会截断自增主键）

```php
Users::clean();  // The table is empty.
```

# 许可证 #

MIT
