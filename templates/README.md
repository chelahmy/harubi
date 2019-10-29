Harubi Templates
================

Harubi templates serve as the starting points for building harubi applications. However, the templates are not the only ways to build harubi applications. The templates highlight harubi approaches.

We will be adding more templates from time to time.

Harubi emphasizes on **model-driven development**. In fact, harubi makes it difficult to do otherwise. Harubi introduces necessary constraints so that every harubi developer will have the same good skills and approaches. Harubi codes will be consistent. Hence, harubi collaboration will be made easier.

Every software application can be formed as a cluster of models. Users ***act*** on models. Hence, the **model-action** pattern is both user and developer friendly.  

In harubi a model is sliced into unique actions. Each action is [served](../docs/beat.md) by a controller. An action focuses on a very specific vertical concern. On the other hand, harubi handles broad horizontal concerns through [presets](../docs/preset.md) and [tolls](../docs/toll.md) which run across controllers. The [User Model](models/user) is an example demonstrating harubi approaches. 
