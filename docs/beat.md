beat()
======

Harubi request router.

## Description

```
void beat(string $model, string $action, string $controller)
```

The beat() function compares $_REQUEST['model'] with $model and $_REQUEST['action'] with $action. If they both matched then the $controller will be invoked and beat() will exit with the return value of the $controller. If the $controller returns an array then beat() will exit with a JSON converted content of the array. Otherwise, beat() will return whatever the $controller returns as-is.

Before invoking the $controller, beat() will assign all matching arguments in the $_REQUEST to the $controller. The $controller is free to have any number of parameters or none.

## Parameters

***model***

The model's name the controller is acting on. 

***action***

The model's action the controller is acting on.

***controller***

The name of the controller function. The controller function can have any number of parameters or none. The beat() function will assign arguments taken from the matching $_REQUEST variables to the controller. If the controller returns an array then it will be converted to JSON format. Otherwise, the return values will be as-is. The return values will be passed to the PHP exit() function.

## Return Values

Nothing, or beat() will exit the process. If the $controller or the global $harubi_permission_controller is invoked then beat() will call the exit() function after any of them returns.

## Examples

```php
// http://example.com/?model=system&action=gettime
beat('system', 'gettime', function ()
{	
	return array(
		'time' => time()
	);
});
```

```php
// http://example.com/?model=user&action=read&name=ali
beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');
	$records = read('user', $where);

	return $records[0];
});
```

```php
// http://example.com/?model=user&action=getpermissions&name=ali

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

## Notes

The beat() function is the backbone of Harubi. There should be as many beat() function calls as to the total of all models actions. A combination of a model and a action should be unique in any beat() function call. There should not be two or more beat() with the same model and action. Otherwise, only the first beat() will be called.

## See Also

[blow()](blow.md)
[create()](create.md)
[read()](read.md)
[update()](update.md)
[delete()](delete.md)
[clean()](clean.md)
[equ()](equ.md)

