$harubi_permission_controller
=============================

The global variable that holds the function name of the permission controller.

##Description

```
global $harubi_permission_controller = null;
```

If the global $harubi_permission_controller contains a function name then the function will be invoked in every beat() call to check whether a model's action is allowed to be executed. If the function return TRUE then the controller for the model's action will be invoked. Otherwise, whatever else the function returns will be passed to the exit() call.

The following is the expected permission controller function format:

```
bool permission_controller(string $model, string $action, string $token);
```

A beat() call will assign the $model, $action, and $token arguments to the permission controller.


##Example

```php
function permission_controller($model, $action, $token)
{
	if ($model == 'user')
	{
		if ($action == 'read')
			return TRUE;
	}

	return array(
		"error" => 1,
		"error_msg" => "Permission denied"
		);
}

$harubi_permission_controller = "permission_controller";
```

##Notes

Permission controller is optional. It is usually applied for access control together with user and role management. The $token variable is meant to identify a user session. 

##See Also

[beat](beat.md)
