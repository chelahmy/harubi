User Model Test
===============

The test script for User Model is implemented in [index.php](https://github.com/chelahmy/harubi/blob/master/templates/models/user/test/index.php). The test needs to access database and create `user` table. Please create `settings.inc` file with the following content:
```json
{
	"globals" : {
		"do_dump_log" : true,
		"do_log_sql_querystring" : true,
		"do_log_presets" : true,
		"do_log_tolls" : true,
		"respond_with_logs" : false
	},

	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi_test",
		"prefix"   : ""
	},

	"tables" : {
		"user" : {
			"id" : "int",
			"name" : "string",
			"password" : "string",
			"email" : "string",
			"created_utc" : "int",
			"updated_utc" : "int"
		}
	}
}
```
And edit `mysql` credentials according to your settings.

Use the PHP development server and navigate to http://localhost:8000. It will run the test script in the `index.php` file.

```
$ php -S localhost:8000
```

