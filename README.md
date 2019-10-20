Harubi
======

## Introduction

Harubi is a MVC-like framework for back-end server, minus the View concern. Nowadays, most clients render Views themselves. The clients may need to access the server only for the storage and persistance. Harubi focuses on **controlling** access to the data **models** at the server side. The absence of the **view** makes Harubi much lighter compares to other back-end MVC frameworks. A Harubi server can serve all types of clients including web-apps, desktops and mobiles. Harubi can also serve APIs.

A Harubi server responds to a request in JSON format.

### Example 1

A client make a request to a Harubi server by specifying at least two arguments: a **model** and an **action** against the model, together with other related **controller** arguments, if exist. The following is a typical request:

```
http://example.com/?model=system&action=gettime
```

And the server is expected to respond with a JSON formatted data set:

```json
{"time":1426835075}
```

The Harubi server implementation looks like the following:

```php
beat('system', 'gettime', function ()
{	
	return array(
		'time' => time()
	);
});
```

The **beat()** call is pulling three arguments: **$model**, **$action** and **$controller**. In the case above the $model is set to *system*, the $action is set to *gettime*, and the $controller is set to a function closure. The beat() function will test whether the request matches with the specified model and action. If it does than the controller will be invoked. If the controller function has parameters then the values will be retrieved from the request arguments. However, in this case the controller has no parameter. The controller is expected to return an array which will be converted into JSON before the beat() function exits. 

The beat process is that whenever the $model and $action matched, the beat() function will invoke the $controller and wait for it to return, convert the return array into json, or leave it as is if the return value is not an array, and then force PHP to exit with the converted/as-is return value as the response. Otherwise, the execution will continue looking for the next beat() calls.

The beat pattern is the Harubi unique way to route requests to controllers.


### Example 2

The following is another example which the controller has a parameter **name**:

```
http://example.com/?model=user&action=read&name=ali
```

```php
beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');

	$records = read('user', $where);

	return $records[0];
});
```

```json
{
    "name" : "ali",
    "email" : "ali@nowhere.com", 
    "created_utc" : 1426835075,
    "updated_utc" : 1426835075
}
```

The **equ()** function sanitizes the *where* equation clause for the SQL *select* query which will be silently generated and issued within the read() function. The sanitization is done on the value of the $name argument and is done so to prevent the database from the infamous *SQL injection* attack.

The **read()** function is one of the implemented CRUD functions in Harubi to simplify the database query processes.

## Model

A model is an abstract dataset with action interfaces. In Harubi a model is sliced into a set of beat() call implementations. Every beat() call is implementing an action for a model. It is easy to associate a model to a table in a database. However, in Harubi a model can become very complex such as involving multiple relational tables. As an example, a user may have a role which defines permissions to access the system. There could be a *getpermissions* action on the *user* model which could involve three tables: user, role and permission.

```php
beat('user', 'getpermissions', function ($name)
{	
	$where = equ('name', $name, 'string');

	$user_records = read('user', $where);

	$role_records = read('role', "name=" . $user_records[0]['role']);

	$perm_records = read('permission', "role_id=" . $role_records[0]['id']);

	return $perm_records;
});
```
 
## Controller

In Harubi, a controller is usually a closure implemention wrapped in a beat() call. Every controller implements a model's action. It is the duty of a controller to make all necessary database queries and form the respective response for the action as requested. The controller is expected to return an array of records.

If a controller is a closure then you may be tricked to see that a beat() call is a controller such as in the model example above, but the beat() call is not the controller. A controller can be implemented as a function for reuse in other beat() calls.

```php
function getpermissions_controller($name)
{
	$where = equ('name', $name, 'string');

	$user_records = read('user', $where);

	$role_records = read('role', "name=" . $user_records[0]['role']);

	$perm_records = read('permission', "role_id=" . $role_records[0]['id']);

	return $perm_records;
}

beat('user', 'getpermissions', 'getpermissions_controller');
```

## Getting Started

A Harubi server is a web server using PHP and MySQL. Please note that this getting started does not include tutorials in PHP and MySQL. Here you are assumed to know the relational database concept and to have the administrative rights to a MySQL server since you are going to create a table in it. You also need to have the administrative rights to a PHP-based web server since you are going to upload some files in it. Most of the time we assume you know what to do with anything related to PHP and MySQL.

First of all, create the following sample user.php file. It declares a user model with two actions: *create* and *read*. Later on you are going to create and read some user records.

**user.php**

```php
include_once 'harubi/harubi.php';

harubi();

beat('user', 'create', function ($name, $password)
{	
	$where = equ('name', $name, 'string');

	$records = read('user', $where);

	if (count($records) <= 0)
	{
		$now = time();
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$id = create('user', array(
			'name' => $name,
			'password' => $hash,
			'created_utc' => $now,
			'updated_utc' => $now
			));	

		if ($id > 0)
			return array(
				'name' => $name,
				'created_utc' => $now,
				'updated_utc' => $now
				);
	}

	return array(
		'error' => 1,
		'error_msg' => 'Could not create user'
		);
});

beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');

	$records = read('user', $where);

	if (count($records) > 0)
	{
		unset($records[0]['id']);
		unset($records[0]['password']);
		return $records[0];
	}

	return array(
		'error' => 1,
		'error_msg' => 'Could not read user'
		);
});
```

In the *user.php* file the call to *harubi()* initiates the Harubi server. The harubi() function sets the database according to the settings in the *settings.inc* file below. Please set your MySQL credential accordingly.

**settings.inc**

```json
{
	"mysql" : {
		"hostname" : "localhost",
		"username" : "root",
		"password" : "secret",
		"database" : "harubi",
		"prefix"   : ""
	},
	
	"tables" : {
		"user" : {
			"id" : "int",
			"name" : "string",
			"password" : "string",
			"created_utc" : "int",
			"updated_utc" : "int"
		}
	}
}
```

You need to create a database and a user table in MySQL that matches with the settings above. You may use the following SQL script to create the table.

```sql
CREATE TABLE IF NOT EXISTS `user` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`password` varchar(64) NOT NULL,
	`created_utc` int(11) NOT NULL,
	`updated_utc` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
);
```

Make sure you install Harubi so that it is accessible via *harubi/harubi.php* as declared in the *user.php* file above.

Now, you may create a user record and read it back. Open a browser and make a query with the following syntax. Make sure to replace *example.com* with your host name.

```
http://example.com/?model=user&action=create&name=ali&password=secret
```

Then, you may read the record back using the following syntax:

```
http://example.com/?model=user&action=read&name=ali
```

Create more user records and read them back randomly for you to get the initial experience of using a Harubi server. You will not be doing things like that often. Most of the time the requests to a Harubi server will be done by its client applications.


## Function List

Initialization and settings:
[harubi](docs/harubi.md)

The Harubi unique request router:
[beat](docs/beat.md)

CRUD functions:
[create](docs/create.md)
[read](docs/read.md)
[update](docs/update.md)
[delete](docs/delete.md)

Sanitization functions:
[clean](docs/clean.md)
[equ](docs/equ.md)






