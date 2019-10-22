harubi()
========

The harubi initialization function.

## Description

```php
void harubi(mixed $settings = 'settings.inc')
```

Initialize harubi with the appropriate settings which includes database credential, table and field mapping.

No, harubi will not take any database administrator away any time soon. You still need to have a good SQL knowledge and experience to use harubi. However, harubi is only interested in object-relational mapping which is a subset of the relational model. At least, you have to create database tables yourself.

harubi needs to know field types: `string`, `integer` or `float`. These will help harubi to sanitize field values and to use quotes properly in constructing SQL string. Thus, you need to specify the field mapping.

The settings has three main components: `globals`, `mysql` and `tables`.

The `globals` settings looks like the following:
```json
{
	"globals" : {
		"do_dump_logs" : true,
		"do_log_sql_querystring" : true,
		"respond_with_logs" : false
	}
}
```
It defines default values of global variables used by harubi.

***do_dump_logs***

When set to true then harubi logs will be written to `harubi.log` file on exit.

***do_log_sql_querystring***

When set to true then harubi will log all sql query strings used to query the database.

***respond_with_logs***

When set to true then harubi will append harubi logs to all responses whenever [respond()](respond.md) functions are used. It will add `logs` item in the JSON response string. **WARNING**: Use this feature only during development. It may expose the system to vulnerability.


The `mysql` settings looks like the following:
```json
{
	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi",
		"prefix"   : ""
	}
}
```
It specifies the `hostname`, `username`, `password`, and `database` as required for MySQL connection. Set `prefix` to tell harubi that all table names in the database has a prefix: `prefix_tablename`. Table name prefix is useful for a single database to have many applications. Harubi is one of the applcation.

The `tables` settings looks like the following:
```json
{
	"tables" : {
		"table1" : {
			"field1" : "integer",
			"field2" : "float",
			"field3" : "string"
		}
	}
}
```
You have to list all tables used in harubi, and map all table fields to harubi field types: `string`, `integer`, or `float`.


## Parameters

***$settings***

If `$settings` is omitted then harubi will load settings from the JSON formatted *settings.inc* file.

The `$settings` value can be in the form of a JSON string or an associative array containing the settings.

## Return Values

Nothing.

## Example

```php
harubi();
```
The harubi() function above will load a *settings.inc* file such as the following:

***settings.inc***
```json
{
	"globals" : {
		"do_dump_log" : true,
		"do_log_sql_querystring" : true,
		"respond_with_logs" : false
	},

	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi",
		"prefix"   : ""
	},
	
	"tables" : {
		"user" : {
			"id" : "integer",
			"name" : "string",
			"password" : "string",
			"created_utc" : "integer",
			"updated_utc" : "integer"
		}
	}
}
```

## Notes

Currently harubi only supports MySQL. The choice for PHP and MySQL was obviously due to the generally available and affordable shared web hostings.

