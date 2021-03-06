delete()
========

Delete database records.

## Description

```php
bool delete(string $table, string $where)
```

Delete database `$table` records according to the given criteria in the `$where` parameter.

The `$table` argument will be sanitized by the delete() function but not `$where`. You have to construct the `$where` clause according to your requirement. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

## Parameters

***$table***

The name of the table where the records are to be deleted.

***$where***

The SQL WHERE clause arguments in the form of string. If `$where` contains only an integer then it is assumed to be the `id` value as in `'id={integer}'`. If `$where` starts with an operator then it is assumed as an operation against the `id` such as `'id>{integer}'`.

`$where` will not be sanitized by the delete() function. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

## Return Values

TRUE if the deletion was successful.

## Examples

```php
delete('user', 'name="ali"'); // delete the user name "ali"
delete('user', equ('name', 'ali', 'string')); // same as above 
```

## Notes

The delete() function simplifies the process to delete records from the database. It also sanitizes the `$table` argument so that to protect the database from the SQL injection attack.

## See Also

[beat()](beat.md)
[blow()](blow.md)
[create()](create.md)
[read()](read.md)
[update()](update.md)
[clean()](clean.md)
[equ()](equ.md)
