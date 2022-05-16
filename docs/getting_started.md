# Getting started

## Installation

```bash
composer require syndesi/neo4j-sync-bundle
```

## Configuration

```yaml
## /config/packages/neo4j_sync.yml
neo4j_sync:
    clients:
        default:
            drivers:
                bolt:
                    url: 'bolt://username:password@localhost'
```

See also [Laudi's own documentation](https://github.com/neo4j-php/neo4j-php-client#url-schemes) for the url schemes.

## Attributes

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Syndesi\Neo4jSyncBundle\Attribute as Neo4jSync;
use Symfony\Component\Serializer\Annotation\Groups;
use Syndesi\Neo4jSyncBundle\Provider\StaticIdentifierProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexNameProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexTypeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

#[Neo4jSync\Node(
    new StaticNodeLabelProvider(new NodeLabel('Node')),
    new SerializerPropertiesProvider(),
    new StaticIdentifierProvider(new Property('id'))
)]
#[Neo4jSync\Index(
    new StaticIndexNameProvider(new IndexName('node_id')),
    new StaticNodeLabelProvider(new NodeLabel('Node')),
    new StaticPropertiesProvider([
        new Property('id')
    ]),
    new StaticIndexTypeProvider()
)]
#[ORM\Entity]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('neo4j')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('neo4j')]
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
```

## Commands

```bash
php bin/console neo4j-sync:index:sync -f
php bin/console neo4j-sync:db:sync -f
```

## Summary

All Doctrine entities of the type Node should now be available inside Neo4j. Take a look at Neo4j's database browser,
usually available at `http://localhost:7474`
