Respond Functions
=================

Harubi published a set of optional respond functions to simplify response formatting by `controller` functions. They are treated as conventional rather than strict. A respond function will return an array with at least a `status` code. The results of a `controller` function will be added to the respond's return array as `results`. On error, the `error_code` and `error_message` will be set.
```php
return ['status'=>1];
```
```
status 0: Error with error_code and error_message.
       1: Ok.
       2: Ok with results.
```

## respond_error()
```php
array respond_error(int $error_code, string $error_message)
```
Response an error with the error code and message.

***$error_code***

The error code as any integer value.

***$error_message***

The error message as string.

### Returns

An array:
```php
['status'=>1, 'error_code'=>$error_code, 'error_message'=>$error_message]
```
