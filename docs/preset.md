preset()
========

A preset is a process run before a controller is invoked. It can stop the controller from being invoked. It can alter the arguments before they are passed to the controller. It can invoke other controllers. And it can do about any other things unrelated to the controller.

All preset functions must be registered prior to calling any routing function ([beat()](beat.md) or [blow()](blow.md)). A preset function is registered using the preset() function:
```php
void preset(string $name, function $preset_func)
```
***$name***

The name of the preset function.

***$preset_func***

The preset function with the following prototype:
```php
mixed preset_func(string $model, $string $action, array &$ctrl_args)
```
***$model***

The model name given by the router.

***$action***

The action name given by the router.

***&$ctrl_args***

A pass-by-reference array of arguments meant to be passed to the controller. The array can be altered by the preset function.

## Example

```php
preset('my-preset', function ($model, $action, &$ctrl_args)
{

});
```
