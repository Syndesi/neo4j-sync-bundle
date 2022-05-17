# Getting started

## Requirements

PHP: 8.1+ due to [enums](https://stitcher.io/blog/php-enums)  
Symfony: 6.0+  
Doctrine ORM: 2.7+

## Installation

Install the package from [Packagist](https://packagist.org/users/Syndesi/packages/) by executing the following command:

```bash
composer require syndesi/neo4j-sync-bundle
```

## Configuration

Currently, this bundle does not provide a [Symfony Flex](https://github.com/symfony/flex) recipe, therefore the
configuration file must be created manually:

```yaml
# /config/packages/neo4j_sync.yml
neo4j_sync:
    clients:
        default:
            drivers:
                bolt:
                    url: 'bolt://username:password@localhost'
```

To see all configurable options you can run `php bin/console config:dump-reference neo4j_sync` and
`php bin/console debug:config neo4j_sync`.

See also [Laudi's own documentation](https://github.com/neo4j-php/neo4j-php-client#url-schemes) for the url schemes.

## Attributes

Add the `Neo4jSync\Node` and `Neo4jSync\Index` to your Doctrine entity class and adapt them if necessary.  
During Doctrine events `SerializerPropertiesProvider` serializes the Doctrine entity and returns an array of `Property`
value objects, which are then added to the Neo4j node.

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

First synchronize the new index. This step is optional, but will improve Neo4j's speed drastically.  

```bash
php bin/console neo4j-sync:index:sync -f
```

Now synchronize existing entities to the Neo4j database:

```bash
php bin/console neo4j-sync:db:sync -f
```

The preceding command might fail on large datasets (~50k+ elements) due to memory limitations. If this happens you can
increase the memory to e.g. 512 MB by appending the option `--memory=512`.

## Summary

All Doctrine entities of the type Node should now be available inside Neo4j. Take a look at Neo4j's database browser,
usually available at `http://localhost:7474`
