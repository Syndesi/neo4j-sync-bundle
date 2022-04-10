# Neo4jSyncBundle

Symfony-bundle for syncing [Doctrine](https://github.com/doctrine/orm) entities to a [Neo4j database](https://neo4j.com/).

It uses the [laudis/neo4j-php-client](https://github.com/neo4j-php/neo4j-php-client) package.

## Features

* Synchronization of normalized Doctrine entities to Neo4j.

## Limitations

* Relations are only supported on the owning side.
* Only one relation per property is permitted.
* Doctrine's `onClear()`-function is not supported.
* Indices aren't automatically created.

## How to use it

First use attributes to mark which entities and properties should be automatically synced:

```php
<?php
namespace App\Entity

#[Neo4jSync\Node(label:"Greeting", id:"id")]    // mark this entity as a Node with the label "Greeting"
                                                // use the property "id" as the identifier
#[ORM\Entity]
class Greeting
{
    #[Neo4jSync\Property]        // mark this variable as a property
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[Neo4jSync\Property]        // mark this variable as a property
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): self
    {
        $this->name = $name;
        return $self;
    }
}
```

Then create new instances and persist them:

```php
<?php

$greeting = new Greeting();
$greeting->setName('Demo name');

$em->persist($greeting);
$em->flush($greeting);
```

The bundle automatically detects the new entity and syncs it to Neo4j. It should now be queryable:

```cypher
MATCH (n) RETURN n
```

Result:

![](./doc/assets/Readme%20Screenshot%20Greeting%20Node.png)

For more advanced configuration check out the following docs:

- Database connections
- Entity serialization
- Relationships

## Commands

This bundle provides several maintenance commands:

```bash
php bin/console neo4j-sync:prune            # clears the Neo4j database
php bin/console neo4j-sync:load             # loads all Doctrine entities into the Neo4j database
                                            # useful if Neo4j database must be reset
php bin/console neo4j-sync:list:connections # lists all available Neo4j database connections
php bin/console neo4j-sync:list:entities    # lists all tracked entities
php bin/console neo4j-sync:stats            # lists some statistics

php bin/console config:dump-reference neo4j_sync
php bin/console debug:config neo4j_sync
```
