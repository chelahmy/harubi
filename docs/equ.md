equ()
=====

Construct a sanitized equation for use in a SQL script.

## Description

```php
string equ(string $name, mixed $value, string $type = 'int', string $op = '=')
```

Construct a string equation such as "name=value" where both the name and the value will be sanitized before the construct. Both the `$name` and the `$value` are assumed to have come from an unmanaged source such as `$_REQUEST`. Sanitization is to protect from the infamous SQL injection attack.

## Parameters

***$name***

The left-side name of the equation.

***$value***

The right-side value of the equation.

***$type***

The type of the value such as `'int'`, `'float'` and `'string'`. The default is `'int'`.

***$op***

The operator in the equation such as `'='`, `'>'`, `'>='`, `'!='`, `'<='`, `'<'`, and `'like'`. The default is `'='`. If the operator is `'like'` then the right-side value will be wrapped in string.

## Return Values

The sanitized equation string.

## Examples

```php
$where = equ('id', 7); // id = 7
$where = equ('name', 'ali', 'string'); // name = "ali"
$where = equ('age', 60, 'int', '<'); // age < 60
$where = equ('name', 'a%', 'string', 'LIKE'); // name LIKE "a%"
```

## Notes

The equ() function is useful in contructing the WHERE clause for a SQL script.

## See Also

[clean()](clean.md)
