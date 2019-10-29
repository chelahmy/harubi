blow()
======

A harubi request router similar to [beat()](beat.md). The different is blow() only operates on url-rewriting friendly request:
```
http://example.com/?q=model/action/controller-param1/...
```
The `q` parameter in the query string contain a slash-separated arguments. The first two arguments are for the model and the action parameters of the blow() function. The rest of the arguments are for the controller. The third argument is for the first parameter of the controller, the fourth argument is for the second parameter, and so on.

Please refer to the [beat()](beat.md) function for details.
