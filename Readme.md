# Neo4jSyncBundle

Symfony-bundle for syncing [Doctrine](https://github.com/doctrine/orm) entities to a [Neo4j](https://neo4j.com/) database.

It uses the [laudis/neo4j-php-client](https://github.com/neo4j-php/neo4j-php-client) package.

## Installation

install the package via composer:

```bash
composer require syndesi/neo4j-sync-bundle
```

Then add a config file in your Symfony project:

```yml
# config/packages/neo4j_sync.yaml
neo4j_sync:
    clients:
        default:
            drivers:
                bolt:
                    url: 'bolt://<neo4j-username>:<neo4j-password>@localhost'
```

## Usage Guide

First [create a doctrine entity class](https://symfony.com/doc/current/doctrine.html#creating-an-entity-class).

After that add the node-attribute at the top of the entity class:

```php
// src/Entity/Greeting.php
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Syndesi\Neo4jSyncBundle\Contract\IndexType;
use Syndesi\Neo4jSyncBundle\Attribute as Neo4jSync;

#[Neo4jSync\Node(
    label: "Greeting",
    id: "id",
    serializationGroup:"neo4j",
    indices: [
        new Neo4jSync\Index('greeting_id_index', IndexType::BTREE, ['id']),
        new Neo4jSync\Index('greeting_name_index', IndexType::TEXT, ['name'])
    ]
)]
#[ORM\Entity]
class Greeting
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups('neo4j')]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('neo4j')]
    public string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }
}
```

Then sync the indices with the following command:

```bash
php bin/console neo4j-sync:index:sync
```

Now the bundle detects new/updated/deleted entities automatically and syncs them to the Neo4j database autonomously:

```php
$greeting = new Greeting();
$greeting->name = 'Demo';

$em->persist($greeting);
$em->flush();
```

```cypher
MATCH (n:Greeting) RETURN n # should return one Greeting node
```

### Getting a Neo4j Client

```php
// src/Service/SomeService.php
namespace App\Service;

use Laudis\Neo4j\Databags\Statement;use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class SomeService
{
    private Neo4jClientInterface $client;

    public function __construct(Neo4jClientInterface $client)
    {
        $this->client = $client;
    }

    public function useClient(): void
    {
        // manually add Neo4j statements to the client's pipeline
        $this->client->addStatement(new Statement('MATCH (n:Greeting) RETURN n', []));
        
        // manually flush the client's pipeline
        $this->client->clear();
        
        // return the internal client (laudis/neo4j-php-client)
        $this->client->getClient();
    }
}
```

Note: The default client used by autowiring is the first configured client. This can be changed by explicitly changing
the configuration `neo4j_sync.default_client`.

Also every configured client gets the service alias `neo4j_sync.neo4j_client.<name>`.

### Normalizing Custom Types

You can provide additional normalization providers via tagging:

```php
// src/Normalizer/RamseyUuidNormalizer.php
<?php

namespace App\Normalizer;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @see https://github.com/jeromegamez/ramsey-uuid-normalizer/blob/master/src/Normalizer/UuidNormalizer.php
 */
class RamseyUuidNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->toString();
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UuidInterface;
    }
}
```

```php
// src/Service/RamesUuidNormalizerProvider.php
<?php
namespace App\Service;

use App\Normalizer\RamseyUuidNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NormalizerProviderInterface;

class RamseyUuidNormalizerProvider implements NormalizerProviderInterface {

    public function getNormalizer(): NormalizerInterface
    {
        return new RamseyUuidNormalizer();
    }
}
```

```yml
# config/services.yaml

services:
    App\Service\RamseyUuidNormalizerProvider:
        tags:
            - name: 'neo4j_sync_normalizer_provider'
              priority: 128
```

The priority defines the order when the normalizer is used. Higher priorities are used first, lower last.

The default normalizers are:

- Neo4jObjectNormalizer, 256
- ObjectNormalizer, 64

## Commands

This bundle provides several maintenance commands:

```bash
php bin/console neo4j-sync:db:delete  # deletes the content of the Neo4j database, optionally including indices
php bin/console neo4j-sync:db:sync    # syncs all entities to the Neo4j database. use --create for faster **initial** sync
php bin/console neo4j-sync:index:list # lists all configured indices and additional indices from the N4oej database itself
php bin/console neo4j-sync:index:sync # syncs all configured indices by first deleting and then creating them

php bin/console config:dump-reference neo4j_sync
php bin/console debug:config neo4j_sync
```
