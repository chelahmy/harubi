harubi()
========

The Harubi initialization function.

## Description

```
void harubi(mixed $settings = 'settings.inc')
```

Initialize Harubi with the appropriate settings which includes database credential settings and field mapping.

No, Harubi will not take database administrators away any time soon. You still need to have a good SQL knowledge and experience to use Harubi. However, Harubi is only interested in object-relational mapping which is a subset of the relational model. At least, you have to create database tables yourself.

Harubi needs to know field types: string, integer or float. These will help Harubi to use quotes properly in constructing SQL string. Thus, you need to specify the field mapping.

The settings has three main components: 'globals', 'mysql' and 'tables'.

The 'globals' settings looks like the following:
```json
{
	"globals" : {
		"do_dump_log" : true,
		"do_log_querystring" : true,
		"respond_with_logs" : false
	}
}
```
It defines default values of global variables used by harubi.

The 'mysql' settings looks like the following:
```json
{
	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi"
	}
}
```
It specifies the 'hostname', 'username', 'password', and 'database' as required for MySQL connection.

The 'tables' settings looks like the following:
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
You have to list all tables used in harubi, and map all fields to Harubi field types: string, integer, or float.


## Parameters

***settings***
If $settings is omitted then Harubi will load settings from the JSON formatted *settings.inc* file.

The $settings value can be in the form of a JSON string or an associative array containing the settings.

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
		"do_log_querystring" : true,
		"respond_with_logs" : false
	},

	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi"
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

Currently Harubi only supports MySQL. The choice for PHP and MySQL was obviously due to the generally available and affordable shared web hostings.

