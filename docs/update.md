update
======

Update database records.

##Description

```
bool update(string $table, array $fields, string $where)
```

Update database records according to the given values and criteria.

All arguments will be sanitized by the update() function except $where. You have to construct the $where clause according to your requirement. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

##Parameters

***table***
The name of the table where records are to be updated into.

***fields***
An associative array of field=value. List only the fields that need to be updated.

***where***
The WHERE clause arguments in the form of string. If $where contains only an integer then it is assumed to be the 'id' value as in 'id={integer}'. If $where starts with an operation then it is assumed as an operation against the 'id' such as 'id>{integer}'.

$where will not be sanitized by the update() function. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

##Return Values

TRUE if the update was successful.

##Examples

```php
update('user', array('name'=>'abu'), 'name="ali"'); // change the user name "ali" to "abu"
update('user', array('name'=>'abu'), equ('name', 'ali', 'string')); // same as above 
```

##Notes

The update() function simplifies the process to update records into the database. It also sanitizes almost all inputs so that to protect the database from the SQL injection attack.

##See Also

[beat](beat.md)
[clean](clean.md)
[create](create.md)
[delete](delete.md)
[equ](equ.md)
[read](read.md)
