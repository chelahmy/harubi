Harubi
======

## Introduction

Harubi is a **[Model-Action Framework](templates/models)** for rapid implementation of back-end services. It is a MVC-like framework minus the View concerns. Nowadays, most front-ends render Views themselves. A front-end may need to access a back-end service only for complex processes and persistance. The absence of **view** makes harubi much lighter and faster compares to other back-end MVC frameworks. A harubi service can serve all types of front-ends including web, desktop and mobile applications. It can also serve other back-end services. Harubi is **API-driven**.

Harubi emphasizes on **model-driven development**. A model is an abstract software definition. A **model** in harubi may not necessarily be an object wrapping a single relational database table. It may be as complex as a relational model with many interrelated tables. Yet, a model may not necessarily be table or object oriented at all. Every request to a harubi service is **acting** on a model. Users ***act*** on models. A request will be routed to a controller that handles a **model-action**. Every controller in harubi is handling an action of a model. See the [user model](templates/models/user) template for a complete code preview on the model-action pattern. And see the [model-action framework](templates/models) to understand the convention.

### Example 1

A front-end makes a request to a harubi service by specifying at least two arguments in the query string, or through a html form post: **model** and **action**, together with **controller** arguments. The following is a typical request:

```
http://example.com/time.php?model=system&action=gettime
```
Or, a url-rewriting friendly request:
```
http://example.com/time.php?q=system/gettime
```

And the service is expected to respond with a JSON formatted dataset:

```json
{"time":1426835075}
```

The harubi service implementation may looks like the following:

```php
beat('system', 'gettime', function ()
{	
	return ['time' => time()];
});
```
The [beat()](docs/beat.md) function is defined as follow:
```php
void beat(string $model, string $action, function $controller)
```
A beat() function call is pulling three arguments: **$model**, **$action** and **$controller**. In the case above the $model is set to `system`, the $action is set to `gettime`, and the $controller is set to a function closure without parameter. The beat() function will test whether the request matches with the specified model and action. If it does than the controller will be invoked immediately. If the controller function has parameters then the values will be retrieved from the request query string. However, in this case the controller has no parameter. The controller is expected to return an array which will be converted into JSON string. 

**The beat process explained:** Whenever the $model and $action matched, the beat() function will invoke the $controller immediately and wait for it to return, convert the returned array values into a JSON string and write it to the PHP output buffer as a response to the request, and disable subsequent beat() or routing function calls. If the controller does not return an array then the returned value will be taken as-is without converting it to a JSON string.

The beat **model-action &rarr; controller** pattern is the harubi unique way to route requests to controllers.

The beat() function has a cousin **[blow()](docs/blow.md)**. They are generally the same except that blow() only accepts request in a url-rewriting friendly format:

```
http://example.com/time.php?q=system/gettime
```

Through out the documentation we will use beat() more often then blow() due to the beat() verbosity on query-string-based request, which is clearer to explain. Anyway, harubi is not designed for front-endings with pretty urls. Yet, it will not stop people from doing so. 


### Example 2

The following is another example which the controller has a **name** parameter:

```
http://example.com/user.php?model=user&action=read&name=ali
```

```php
beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');
	$records = read('user', $where);

	return $records[0];
});
```
The `name` parameter from the request query string which contain the argument `ali` will be matching the controller `$name` parameter. Hence, `ali` will be assigned to the controller `$name` parameter as argument.

The response may look like the following:
```json
{
    "name" : "ali",
    "email" : "ali@nowhere.com", 
    "created_utc" : 1426835075,
    "updated_utc" : 1426835075
}
```

The **[equ()](docs/equ.md)** function sanitizes the *where* equation clause for the SQL *select* query which will be silently generated and issued within the read() function. The sanitization is done on the value of the $name argument, and is done so to prevent the database from the infamous *SQL injection* attack.

The **[read()](docs/read.md)** function is one of the implemented CRUD functions in harubi to simplify the database query processes.

## Model

A model is an abstract software definition with action interfaces. In harubi a model is sliced into a set of beat() implementations. Every beat() is implementing a response to an action of a model. It is easy to associate a model to a table in a database. However, in harubi a model can become very complex such as involving multiple relational tables, or no table at all as in the get time example above. In a typical service example, a user may have a role which defines permissions to access the service. There could be a *getpermissions* action on a *user* model which could involve three tables: user, role and permission.

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

In harubi, a controller is usually a closure wrapped in a beat() call. Every controller implements a model's action. It is the duty of a controller to make all necessary system or database queries and form the response for the action as requested. The controller is expected to return results in an array, but not necessarilly.

If a controller is a closure then you may think that a beat() is a controller such as in the model example above, but the beat() is not the controller. A controller can be implemented as a function for reuse in other beat() calls.

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
### Presets and Tolls

This is a short introduction to [presets](docs/preset.md) and [tolls](docs/toll.md). It is an advance subject that is worth exploring.

A controller can be injected with presets and tolls to condition the final response to a request. A preset may keep records of a request to a controller, alter a controller arguments, or bypass the controller altogether. A toll may alter a controller results, or bypass the controller results altogether. Hence, presets and tolls are opening many possibilities to controller designs.

## Getting Started

A harubi server is a web server using PHP and MySQL. Please note that this getting started does not include tutorials on PHP and MySQL. Here you are assumed to know the relational database concept and to have the administrative rights to a MySQL server since you are going to create a table on it. You also need to have the administrative rights to a PHP-based web server since you are going to upload some files on it. Most of the time we assume you know what to do with anything related to PHP and MySQL.

We are going to create a user model with *create* and *read* actions.

Create the following sample *user.php* file. It declares a user model with two actions: *create* and *read*. Later on you are going to create and read some user records.

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
		$id = create('user', [
			'name' => $name,
			'password' => $hash,
			'created_utc' => $now,
			'updated_utc' => $now
			]);	

		if ($id > 0)
			return [
				'name' => $name,
				'created_utc' => $now,
				'updated_utc' => $now
				];
	}

	return [
		'error' => 1,
		'error_msg' => 'Could not create user'
		];
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

	return [
		'error' => 1,
		'error_msg' => 'Could not read user'
		];
});
```

In the *user.php* file the call to *harubi()* initiates harubi. The harubi() function sets the database according to the settings in the *settings.inc* file below. Please set your MySQL credential accordingly.

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

You need to create a database with a *user* table on MySQL similar to the settings above. You may use the following SQL script to create the table.

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

The `harubi.php` file is expected to be at *harubi/harubi.php* as declared in the *user.php* file above.

Now, you may create a user record and read it back. Start the PHP development server:
```
$ php -S localhost:8000
```
Open a browser and make a query with the following syntax.
```
http://localhost:8000/user.php?model=user&action=create&name=ali&password=secret
```
Then, you may read the record back using the following syntax:
```
http://localhost:8000/user.php?model=user&action=read&name=ali
```

Create more user records and read them back randomly. Do it more for you to get the initial experience of using a harubi service. You will not be doing these things often. Most of the time requests to a harubi service will be done by another applications such as a front-end.


## Function List

Every function has a page in the [documentation](docs) folder. Harubi documentation is bigger than its code base. Every folder in this repository has a README.md file describing its contents. GitHub will display the README below the file listing of the folder.

Initialization and settings:
[harubi()](docs/harubi.md)

The harubi unique request handlers:
[beat()](docs/beat.md)
[blow()](docs/blow.md)
[respond()](docs/respond.md)
[preset()](docs/preset.md)
[toll()](docs/toll.md)

CRUD functions:
[create()](docs/create.md)
[read()](docs/read.md)
[update()](docs/update.md)
[delete()](docs/delete.md)

Sanitization functions:
[clean()](docs/clean.md)
[equ()](docs/equ.md)

Debugging with harubi:
[debugging functions](docs/debugging.md)





