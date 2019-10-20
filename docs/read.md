read()
======

Read database records.

## Description

```php
array read(string $table, mixed $fields = FALSE, string $where = FALSE, string $order_by = FALSE, string $sort = FALSE, int $limit = FALSE, int $offset = FALSE, bool $count = FALSE)
```
Read records from `$table` that meet with the given criteria. The read() function construct the SQL SELECT statement filled with the values from the given arguments whichever are not set to FALSE. Then, the read() function makes the SELECT query against the database, and return records.

All arguments will be sanitized by the read() function except `$where`. You have to construct the `$where` clause according to your requirement. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

The SELECT statement will have the following format:
```sql
SELECT $fields FROM $table WHERE $where ORDER BY $order_by $sort LIMIT $offset, $limit;
```
When `$fields` is FALSE then all fields `'*'` will be included.

When no other criteria is given then all records will be returned:
```sql
SELECT * FROM $table;
```

The followings are possible:
```sql
SELECT * FROM $table WHERE $where;
SELECT * FROM $table WHERE $where ORDER BY $order_by;
SELECT * FROM $table WHERE $where ORDER BY $order_by $sort;
SELECT * FROM $table WHERE $where ORDER BY $order_by $sort LIMIT $limit;
SELECT * FROM $table WHERE $where ORDER BY $order_by $sort LIMIT $offset, $limit;
SELECT * FROM $table WHERE $where ORDER BY $order_by $sort LIMIT $offset, 18446744073709551615;
SELECT * FROM $table WHERE $where LIMIT $limit;
SELECT * FROM $table WHERE $where LIMIT $offset, $limit;
SELECT * FROM $table WHERE $where LIMIT $offset, 18446744073709551615;
SELECT * FROM $table;
SELECT * FROM $table ORDER BY $order_by;
SELECT * FROM $table ORDER BY $order_by $sort;
SELECT * FROM $table ORDER BY $order_by $sort LIMIT $limit;
SELECT * FROM $table ORDER BY $order_by $sort LIMIT $offset, $limit;
SELECT * FROM $table ORDER BY $order_by $sort LIMIT $offset, 18446744073709551615;
SELECT * FROM $table LIMIT $limit;
SELECT * FROM $table LIMIT $offset, $limit;
SELECT * FROM $table LIMIT $offset, 18446744073709551615;
```
 
If the `$count` is set to TRUE then the followings are possible:
```sql
SELECT COUNT(*) as count FROM $table WHERE $where;
SELECT COUNT(*) as count FROM $table WHERE $where LIMIT $limit;
SELECT COUNT(*) as count FROM $table WHERE $where LIMIT $offset, $limit;
SELECT COUNT(*) as count FROM $table WHERE $where LIMIT $offset, 18446744073709551615;
SELECT COUNT(*) as count FROM $table;
SELECT COUNT(*) as count FROM $table LIMIT $limit;
SELECT COUNT(*) as count FROM $table LIMIT $offset, $limit;
SELECT COUNT(*) as count FROM $table LIMIT $offset, 18446744073709551615;
```

## Parameters

***$table***

The name of the table where records are to be read from.

***$fields***

The fields of the records, which are to be returned. If there are more than one fields then they must be listed in an array. Use string for a single field. If `$fields` is FALSE then all fields `'*'` will be returned. The default is FALSE.

***$where***

The SQL WHERE clause arguments in the form of string. If `$where` contains only an integer then it is assumed to be the `id` value as in `'id={integer}'`. If `$where` starts with an operator then it is assumed as an operation against the `id` such as `'id>{integer}'`.

`$where` will not be sanitized by the read() function. You may use [clean()](clean.md) and [equ()](equ.md) to sanitize inputs from unmanaged sources.

If `$where` is set to FALSE then it will not be specified in the SQL SELECT statement. The default is FALSE.

***$order_by***

The SQL ORDER BY clause argument which is usually a field name. It may be followed by `$sort`. If `$order_by` is set to FALSE then it will not be specified in the SQL SELECT statement. The default is FALSE.

***$sort***

Use in conjunction with `$order_by`. The value is either `'ASC'` for ascending order or `'DESC'` for decending order. If `$sort` is set to FALSE then it will not be specified in the SQL SELECT statement, but usually will be assumed as `'ASC'`. The default is FALSE.

***$limit***

The maximum number of records to be read. It is a part of the SQL LIMIT clause which may be used together with `$offset`. If `$limit` is set to FALSE then it will not be specified in the SQL SELECT statement. The default is FALSE.

***$offset***

Skip all records prior to the offset point. This is useful in reading a page or a segment of records. It is a part of the SQL LIMIT clause which may be used together with `$limit`. If `$offset` is set to FALSE then it will not be specified in the SQL SELECT statement. The default is FALSE.

***$count***

If set to TRUE then the count of the records will be returned instead of the records. The default is FALSE.

## Return Values

|Returns|
|-------|
|An array containing the records.|
|If `$count` is TRUE then the count of the records as array("count" => count).|
|FALSE when failed to connect to the database.|

## Examples

```php
$records = read('user'); // all user records will be returned
$records = read('user', FALSE, 7); // the user with id = 7 will be returned
$records = read('user', FALSE, '>7'); // the users with id larger than 7 will be returned
$records = read('user', FALSE, 'name="ali"'); // the user with the name "ali" will be returned
$records = read('user', FALSE, equ('name', 'ali', 'string')); // same as above
$records = read('user', 'email', equ('name', 'ali', 'string')); // same as above but only email
$records = read('user', array('name', 'email')); // all user names and emails
$records = read('user', FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, TRUE); // the count of all records
```

## Notes

The read() function is not designed to replace SQL SELECT. harubi is only interested in object-relational maping (ORM). Thus, many relational models for SELECT are not directly supported.

The read() function simplifies the process to read records from the database based on the ORM principle. It also sanitizes almost all inputs so that to protect the database from the SQL injection attack.

## See Also

[beat()](beat.md)
[blow()](blow.md)
[create()](create.md)
[update()](update.md)
[delete()](delete.md)
[clean()](clean.md)
[equ()](equ.md)
