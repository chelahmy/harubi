Preset
======
A preset is a process run before a controller is invoked. It can stop the controller from being invoked. It can alter the arguments before they are passed to the controller. It can invoke other controllers. And it can do about any other things unrelated to the controller.

All preset functions must be registered prior to calling any routing function ([beat()](beat.md) or [blow()](blow.md)). A preset function is registered using preset() function.

## preset()
```php
void preset(string $name, function $preset_func)
```
### Arguments

***$name***

The name of the preset function.

***$preset_func***

The preset function with the following prototype:
```php
mixed preset_func(string $model, string $action, array &$ctrl_args)
```
### Preset function arguments

***$model***

The model name given by the router.

***$action***

The action name given by the router.

***&$ctrl_args***

A *pass-by-reference* array of arguments given by the router meant to be passed to the controller. The array can be altered by the preset function before they are passed to the controller.

## Example

Stopping the controller.
```php
preset('permission', function ($model, $action, &$ctrl_args)
{
	global $user;
	
	if (!$user['authenticated'])
		return respond_error(1, "Access denied");
});
```
A preset that returns something will stop the controller from being invoked. The return value will be used as the response to the request that called the preset. If a preset does not return anything then the controller will be invoked.

## Notes

Once a preset is called it means a request is being processed and harubi will exit when the router finish. Hence, always handle a preset from a request point of view. A preset can decide on behalf of a controller. A preset may be there to take a chunk of the controller responsibility. A preset may simplify controller design.

## See Also

[toll()](toll.md)
[beat()](beat.md)
[blow()](blow.md)
[respond()](respond.md)


