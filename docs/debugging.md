Debugging with Harubi
=====================

Harubi published a set of optional debugging functions to help with debugging harubi service. Debugging is making used of optional harubi memory logs.

## Harubi Memory Logs

Harubi implements a simple memory logs to help tracking activities within a service to a request. It is a way to verify that a service is doing its job properly. The logs can be turned off in production mode. There is a set of global variables that control the logging behaviors which can be defined in [harubi()](harubi.md) settings.
