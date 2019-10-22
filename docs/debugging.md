Debugging with Harubi
=====================

Harubi published a set of optional debugging functions to help with debugging harubi service. Debugging is making used of optional harubi memory logs.

## Harubi Memory Logs

Harubi implements a simple memory logs to help tracking activities within a service to a request. It is a way for developer to verify that a service is doing its job properly. The logs can be turned off in production mode. There is a set of global variables that control the logging behaviors which can be defined in [harubi()](harubi.md) settings.

The `globals` in harubi settings looks like the following:
```json
{
	"globals" : {
		"do_dump_logs" : true,
		"do_log_sql_querystring" : true,
		"respond_with_logs" : false
	}
}
```
It defines default values of global variables used by harubi, which in this case is defining logging behaviors.

***do_dump_logs***

When set to true then harubi logs will be written to `harubi.log` file on exit.

***do_log_sql_querystring***

When set to true then harubi will log all sql query strings used to query the database.

***respond_with_logs***

When set to true then harubi will append harubi logs to all responses whenever [respond()](respond.md) functions are used. It will add `logs` item in the JSON response string. **WARNING**: Use this feature only during development. It may expose the system to vulnerability.

## Logging Functions

### harubi_log()

```php
null harubi_log(mixed $file, mixed $function, mixed $line, mixed $type, string $message)
```
Create a new entry into the memory logs.

***$file***

The PHP file name as the logging reference. May use the PHP magic constant `__FILE__` which gives the filesystem path to the current php file.

***$function***

The function name as the logging reference. May use the PHP magic constant `__FUNCTION__` which gives the current function name.

***$line***

The line number as the logging reference. May use the PHP magic constant `__LINE__` which gives the current line number.

***$type***

The user defined type of the log.

***$message***

The user defined message for the log.

### harubi_log_debug()

```php
null harubi_log_debug(mixed $line, string $message)
```
Similar to `harubi_log()` where the `$type` is set to `'debug'` and both `$file` and `$funtion` are unset. Use this function only for active debugging.

### get_harubi_logs()

```php
array get_harubi_logs()
```
Get the memory log array.

### print_harubi_logs()

```php
null print_harubi_logs()
```
Print the memory logs as PHP output. It does not play nice with harubi standard response. use this function only for active debugging.

### dump_harubi_logs()

```php
null dump_harubi_logs()
```
Dump the memory logs into `harubi.log` file. The file will be overwritten with the latest logs.






