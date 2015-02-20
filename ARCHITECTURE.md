Gravity
=======

A modular PHP architecture in four main ideas:

  - Container based configuration.
  - Distributed bootstrap approach.
  - Pure relational persistence layer.
  - Modules should require no dependencies.

Container Based Configuration
-----------------------------

Gravity runtime is defined by a dependency injection container.

Instead of connecting its componets by code, Gravity relies on lazy injected
objects read from configuration in order to build most of its objects.

This is how manually injected code looks like:

```php
$connection = new PDO('sqlite::memory:');
$db     = new Respect\Relational\Db($connection);
$mapper = new Respect\Relational\Mapper($db);
```

This is how configurable dependency injection looks like:

```ini
[connection PDO]
dsn = sqlite::memory:

[db Respect\Relational\Db]
connection = [connection]

[mapper Respect\Relational\Mapper]
db = [db]
```

In the last example though, instead of instantly building all three objects,
the container will build them only when requested:

```
// Builds connection (required for db), then builds db and returns it.
$db = $container->db;
```

Most of Gravity blueprint is defined by containers like these.

Distributed Bootstrap Approach
------------------------------

Gravity is built from configurable containers right from its core. Its module
system works by auto discovering container configuration from vendor
packages:

```ini
; Excerpt from a supercluster.ini file.

; Local configuration
load[] = etc/gravity.ini
load[] = etc/hypermedia.ini
load[] = supercluster.ini
load[] = etc/application.ini
load[] = etc/datasources.ini

; External vendor containers
boot[] = vendor/vendorname/modulename/supercluster.ini
```

Pure Relational Persistence Layer
---------------------------------

To persist data, Gravity uses an approach designed to leverage relational
data. Multiple modules can contribute to the combined database schema, built
by configurable containers without conflicting with each other.

```ini

[catalogModelSchema Supercluster\Gravity\Datasources\SchemaContribution]
  subscribeType[]  = [id,            'INT PRIMARY KEY']
  subscribeType[]  = [name,          'VARCHAR(255)']
  subscribeType[]  = [Product,       'INT REFERENCES Product(id)']
  subscribeType[]  = [Model,         'INT REFERENCES Model(id)']
  subscribeType[]  = [Product_Model, 'INT REFERENCES Product_Model(id)']
  subscribeRange[] = [Product, id]
  subscribeRange[] = [Model, id]
  subscribeRange[] = [Product_Model, id]
  subscribeRange[] = [Product_Model, Product]
  subscribeRange[] = [Product_Model, Model]

```

Modules provide their full database graph, and multiple modules can interact
by reaching consensus over the entire schema. To ensure consensus can be met,
a set of naming and structural conventions are applied.

Gravity's persistence layer is nothing but a set of contracts designed to
require minimal tooling behind them.


Modules should have no dependencies.
------------------------------------

To achieve modularity, Gravity relies on most limitations of both REST for
the model context and CQRS for the persistence approach.

A Gravity module doesn't need mandatory dependencies by embracing modularity
in all of its layers:

```php

// Core function should be exposed on traits
trait MyHelloWorldTrait
{
	public function helloWorld()
	{
		return 'Hello World';
	}
}

// Independent implementations don't need to specify vendor dependencies
class MyHelloWorld
{
	use MyHelloWorldTrait;
}

// Vendor-dependent implementations can be provided without requiring them
class MyRoutableHelloWorld implements Respect\Rest\Routable
{
	use MyHelloWorldTrait;

	public function get()
	{
		return ['message' => $this->helloWorld()];
	}
}

```

Instead of hard coupling modules to Gravity, modules themselves require only
their specific suggested "natively pluggable" compatible vendors.

```json
    "suggest": {
        "respect/rest": "Allows routing this module using REST.",
        "respect/data": "Allows automatic persistence using data collections.",
        "respect/config": "Allows accessing pre-configurable containers.",
        "hello/lorem": "More sample texts available as modules."
    }
```

Containers can be provided if Respect\Config is available. Modules fall back
by design to their native PHP APIs when a container isn't available.

```ini
[helloWorldRoute]
  method = GET
  pattern = /hello-world
  class = MyRoutableHelloWorld

[router Respect\Rest\Router]
  appendRoute[] = [helloWorldRoute]
```

A fully plug-and-play module for gravity can be completely independent, even
though it provides interfaces for mainly two components: how it routes data
and how it persists data.

The technical implementation follows the REST over CQRS styles, suggesting
interfaces that segregate queries from commands.

Persistence is built in but loosely coupled using a generic interface and a
highly specialized mapper implementation backed up by conventions.

Any conventions are naturally expressed as both pure object dependencies and
configuration.

