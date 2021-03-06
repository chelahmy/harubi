beat()
======

A harubi request router.

## Description

```php
void beat(string $model, string $action, function $controller)
```

The beat() function compares `$_REQUEST['model']` with `$model` and `$_REQUEST['action']` with `$action`. If they both matched then the `$controller` will be invoked and beat() will exit with the return value of the `$controller`. If the `$controller` returns an array then beat() will exit with a JSON converted content of the array. Otherwise, beat() will return whatever the `$controller` returns as-is.

Before invoking the `$controller`, beat() will assign all matching arguments in the `$_REQUEST` to the `$controller`. The `$controller` is free to have any number of parameters, or none.

A get request may look like the following:
```
http://example.com/time.php?model=system&action=gettime
```
beat() also accepts url-rewriting friendly request:
```
http://example.com/?q=model/action/controller-param1/...
```
The `q` parameter in the query string contain a slash-separated arguments. The first two arguments are for the `$model` and the `$action` parameters of the beat() function. The rest of the arguments are for the controller. The third argument is for the first parameter of the controller, the fourth argument is for the second parameter, and so on.

**Note:** If `q` parameter exists in a request query string, which is meant for a url-rewriting friendly request, then beat() will use it and ignore other parameters such as `model` and `action`. You may want to use [blow()](blow.md) instead which responds only to url-rewriting friendly requests.

## Parameters

***$model***

The model's name the controller is acting on. 

***$action***

The model's action the controller is acting on.

***$controller***

The name of the controller function. The controller function can have any number of parameters or none. The beat() function will assign arguments taken from the matching `$_REQUEST` variables to the controller. If the controller returns an array then it will be converted to JSON format. Otherwise, the return values will be as-is. The return values will be passed to the PHP exit() function.

## Return Values

Nothing. beat() will echo the response string with the `$controller` results, and terminate the routing.

## Examples

```php
// http://example.com/time.php?model=system&action=gettime
beat('system', 'gettime', function ()
{	
	return ['time' => time()];
});
```

```php
// http://example.com/user.php?model=user&action=read&name=ali
beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');
	$records = read('user', $where);

	return $records[0];
});
```

```php
// http://example.com/user.php?model=user&action=getpermissions&name=ali

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

## Conventional Response

A function should return the expected results. However, there are many instances where a function will not be able to return as expected due to many circumstances. Hence, a good function should always return a status with reasons.

A controller may not always be able to return the expected results. Hence, it must always return a status so that the response to a request, which is handled by the controller, will be in turn handled appropriately.

Harubi published a set of [respond()](respond.md) functions to help with controller returns. It is more conventional than strict. An application may want to handle its responses differently. However, the [harubi test framework](../test) relies on it.

The following is an example of a controller return with [respond_ok()](respond.md):
```php
// http://example.com/time.php?model=system&action=gettime
beat('system', 'gettime', function ()
{	
	return respond_ok(['time' => time()]);
});
```

## Notes

The beat() function is the backbone of harubi. There should be as many beat() function calls as to the total of all models + actions. A combination of a model and a action should be unique in any beat() function call. There should not be two or more beat() with the same model and action. Otherwise, only the first beat() will be called.

## See Also

[blow()](blow.md)
[respond()](respond.md)
[create()](create.md)
[read()](read.md)
[update()](update.md)
[delete()](delete.md)
[clean()](clean.md)
[equ()](equ.md)

