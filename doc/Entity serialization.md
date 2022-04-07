# Entity serialization

This bundle uses [Symfony's Serializer Component](https://symfony.com/doc/current/components/serializer.html) for
reading properties from Doctrine entities.

**Note**: Only primitive properties are saved "as is" into Neo4j. More complex data types like arrays are encoded into
JSON and stored as text. See also [Neo4j's values and types](https://neo4j.com/docs/cypher-manual/current/syntax/values/).

Relationships should not be serialized by Symfony, instead use the provided relationship-attributes.

```php
namespace Acme;

use Symfony\Component\Serializer\Annotation\Groups;

#[Neo4jSync\Node(label:"MyObj", id:"id", serializerGroup:"neo4j")] 
class MyObj
{
    #[@Groups('neo4j')]
    public $foo;
    
    #[@Groups('neo4j')]
    public $foo;

    #[@Groups('neo4j')]
    public function getBar()
    {
        return $this->bar;
    }
}
```


id
annotation
relations
