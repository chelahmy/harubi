clean()
=======

Sanitize a value before assigning it to a SQL script.

## Description

```php
mixed clean(mixed $value, string $type = 'int', $like = FALSE)
```

Sanitize the $value for later use in a SQL script. The $value is assumed to have come from an unmanaged source such as $_REQUEST. Sanitization is to protect from the infamous SQL injection attack.


## Parameters

***value***

The value to be sanitized.

***type***

The expected type of the value that could be 'int', 'float' or 'string'. 'int' is the default. As an example, if the value is a string and the type is 'int' then the value will be converted to integer.

A string value will be escaped. 

***like***

Set this to TRUE if the value is to be used in the SQL's LIKE clause. The default is FALSE.

## Return Values

The sanitized value.

## Examples

```php
$val1 = clean(3); // 3
$val2 = clean('2'); // 2
$val3 = clean('2.1', 'float'); // 2.1
$val4 = clean('abc', 'string'); // 'abc'
$val5 = clean(5, 'string'); // '5'
```

## Notes

The clean() function is not meant to be used in every $_REQUEST or anything like it. Sanitization is only useful for values that are going to be included in SQL scripts.

## See Also

[equ()](equ.md)
