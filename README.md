Gravity
=======

A modular PHP architecture in four main components:

  - Container based configuration.
  - Distributed bootstrap approach.
  - Pure relational persistence layer.
  - REST over CQRS.

Container Based Configuration
-----------------------------

Gravity runtime is defined by a dependency injection container.

Instead of connecting its componets by code, Gravity relies on lazy injected
objects read from configuration in order to build most of its objects.

Manually injected code:

```php
$connection = new PDO('sqlite::memory:');
$db     = new Respect\Relational\Db($connection);
$mapper = new Respect\Relational\Mapper($db);
```

Configurable dependency injection:

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

[catalogProductsSchema Supercluster\Gravity\Datasources\SchemaContribution]
  subscribeProperty[] = [id, 'INTEGER PRIMARY KEY']
  subscribeProperty[] = [full_name, 'VARCHAR(255)']
  subscribeRange[]    = [product, id]
  subscribeRange[]    = [product, full_name]
  subscribeRange[]    = [offer, id]
  subscribeRange[]    = [offer, full_name]

```

REST over CQRS
--------------

These two styles are a perfect fit. REST encourages an uniform interface
focused on state transfers, and CQRS encourages a clear separation between
state lookups and state changes.

