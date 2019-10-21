Respond Functions
=================

Harubi published a set of optional respond functions to simplify response formatting by `controller` functions. They are treated as conventional rather than strict. A respond function will return an array with at least a `status` code. The results of a `controller` function will be added to the respond's return array as `results`. On error, the `error_code` will be set.
```php
return ['status'=>1];
```
```
status 0: Error with error_code.
       1: Ok.
       2: Ok with results.
```
