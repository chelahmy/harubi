beat
====

Harubi request router.

##Description

```
void beat(string $model, string $action, string $controller)
```

The beat() function compares $_REQUEST['model'] with $model and $_REQUEST['action'] with $action. If they both matched then the $controller will be invoked and beat() will exit with the return value of the $controller. If the $controller returns an array then beat() will exit with a JSON converted content of the array. Otherwise, beat() will return whatever else the $controller returns as is.

Before invoking the $controller, beat() will assign all matching arguments with the $_REQUEST to the $controller. The $controller is free to have any number of parameters or none.

If the global $harubi_permission_controller is set then it will be invoked before the $controller. Next, if the $harubi_permission_controller does not return TRUE then beat() will not invoke the $controller and instead beat() will exit with the return value of the $harubi_permission_controller as JSON when it is an array, or as is.

The $harubi_permission_controller function should have three parameters: $model, $action, and $token. Both values of $model and $action will be taken from the matching beat() arguments. The $token value will be taken from $_REQUEST if available. Otherwise, the $token value will be set to an empty string.

##Parameters

***model***

The model's name the controller is acting on. 

***action***

The model's action the controller is acting on.

***controller***

The name of the controller function. The controller function can have any number of parameters or none. The beat() function will assign arguments taken from $_REQUEST variable to the controller. If the controller returns an array then it will be converted to JSON format. Otherwise, the return values will be taken as is. The return values will be passed to the exit() function.

##Return Values

Nothing or not expecting to return. If the $controller or the global $harubi_permission_controller is invoked then beat() will call the exit() function after any of them returns.

##Examples

```php
beat('system', 'gettime', function ()
{	
	return array(
		'time' => time()
	);
});
```

```php
beat('user', 'read', function ($name)
{	
	$where = equ('name', $name, 'string');

	$records = read('user', $where);

	return $records[0];
});
```

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

##Notes

The beat() function is considered to be the main function in Harubi. There should be as many beat() functions calls as to the sum of the models actions. The combination of the model and the action should be unique in any beat() function call.

##See Also

[$harubi_permission_controller](harubi_permission_controller.md)

[clean](clean.md)
[create](create.md)
[delete](delete.md)
[equ](equ.md)
[read](read.md)
[update](update.md)

