Toll
====
A toll is a process run after a controller was invoked. It can alter or replace the controller results. It can do about any other things.

All toll functions must be injected prior to calling any routing function ([beat()](beat.md) or [blow()](blow.md)). A toll function is injected using toll() function.

## toll()
```php
void toll(string $name, function $toll_func)
```
### Arguments

***$name***

The name of the toll function.

***$toll_func***

The toll function to be injected. It must be in the form of the following prototype:
```php
mixed toll_func(string $model, string $action, array $ctrl_args, array &$ctrl_results)
```
### Tool function arguments

***$model***

The model name given by the router.

***$action***

The action name given by the router.

***$ctrl_args***

The array of arguments which was passed to the controller.

***&$ctrl_results***

The *pass-by-reference* array of results from the controller.

## Example

Cancelling the controller results.
```php
toll('watchdog', function ($model, $action, $ctrl_args, &$ctrl_results)
{
	$json = json_encode($ctrl_results);
	
	if (strpos($json, '*@#%&*!!') !== false)
		return respond_error(1, "The response contains rude word");
});
```
A toll that returns something will bypass the controller results. The return value will then be used as the response to the request that called the toll. If a toll does not return anything then the controller results will be used as the response to the request.

## Notes

Once a toll is called it means a request is being processed and harubi will exit when the router finish. Hence, always handle a toll from a request point of view. A toll may act against a controller results. A toll may be there to remove a chunk of the controller responsibility.

There could be a chain of toll functions. Each may alter the controller results. And the last one may return. These open up many possiblities in controller design.

## See Also

[preset()](preset.md)
[beat()](beat.md)
[blow()](blow.md)
[respond()](respond.md)


