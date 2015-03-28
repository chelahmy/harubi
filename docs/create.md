create
======

Create a new record in a database.

##Description

```
int create(string $table, array $fields)
```

Insert a new record containing the $fields into the $table in a database. No need to sanitize the the $table and the $fields because they will be sanitized by create() prior to being passed to the database.

##Parameters

***table***

The name of the table where the new record is to be created in.

***fields***

The name-value pair fields of the new record. Every record has the 'id' field which does not have to be specified during record creation. The 'id' field will be set with a unique serial number.

##Return Values

|Returns|Description                         |
|-------|------------------------------------|
|>0     | The new record id.                 |
|-1     | Failed to connect to the database. |
|-2     | Failed to create the record.       |

##Examples

```php
$id = create('user', array('name' => 'ali', 'password' => 'secret'));
```
The create() call above will create the following record:

|Name    |Value |
|--------|------|
|id      |>0    |
|name    |ali   |
|password|secret|

##Notes

The create() function simplifies the process to insert a record into a database. It also sanitizes all inputs so that to protect the database from the SQL injection attack.

##See Also

[beat](#beat.md)
[clean](#clean.md)
[delete](#delete.md)
[equ](#equ.md)
[read](#read.md)
[update](#update.md)
