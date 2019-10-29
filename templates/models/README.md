Models
======

A **model** is an abstract software definition denoting user needs. A model definition should be actionable to both users and developers. Users **act** on models. In harubi, a model is *vertically* sliced into unique implementable actions. The word vertical is also an abstract definition. We will discuss vertical and horizontal slices shortly. An **action** is implemented as a **controller** which is a software code serving a user's action. A controller may process inputs, persist data, trigger external systems, or do anything in the realm of software. A controller responds to a user request through an action.

Generally, a model invokes a collection of controllers. A *vertical* slice of action is implemented as a vertical slice of controller. A vertical slice is a top-down process where a user's action at the top is carried out by a controller down to the bottom of a system. It will respond exactly to a user request through an action. However, a controller may need to handle few tasks that need to be applied across all actions such as access control and logging. Hence, a *horizontal* slice is referring to a task across all controllers. A horizontal action may handle an access control task, a logging task, etc. Thus, a vertical controller is freed from the horizontal tasks. In harubi, a model's concerns is being sliced vertically and horizontally. Hence, controller design is simplified.

Vertical slicing is handled by [beats](../../docs/beat.md) and [blows](../../docs/blow.md). And horizontal slicing is handled by [presets](../../docs/preset.md) and [tolls](../../docs/toll.md).

