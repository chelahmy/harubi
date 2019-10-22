Debugging with Harubi
=====================

Harubi published a set of optional debugging functions to help with debugging harubi service. Debugging is making used of optional harubi memory logs.

## Harubi Memory Logs

Harubi implements a simple memory logs to help tracking activities within a service to a request. It is a way to verify that a service is doing its job properly. The logs can be turned off in production mode. There is a set of global variables that control the logging behaviors which can be defined in [harubi()](harubi.md) settings.

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

