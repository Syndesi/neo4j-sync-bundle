# Relationships

**Note**: All limitations of Doctrine's relations apply to this bundle, namely:
> Doctrine will only check the owning side of an association for changes. - *[source](https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/unitofwork-associations.html#association-updates-owning-side-and-inverse-side)*

This means that relations are only updated if the owning side gets updated **and** contains relation-attributes.


```php
namespace Acme;

use Symfony\Component\Serializer\Annotation\Groups;

#[Neo4jSync\Node(label:"Parent", id:"id", serializationGroup:"neo4j")] 
class Parent
{
    #[@Groups('neo4j')]
    public $id;
    
    #[@Groups('neo4j')]
    public $name;
}


#[Neo4jSync\Node(label:"Child", id:"id", serializationGroup:"neo4j")]
#[Neo4jSync\Relation([
    [label:"CHILD_PARENT_RELATION", targetLabel:"Parent", targetProperty:"id", targetValue:"parent"]
])]
class Child
{
    #[@Groups('neo4j')]
    public $id;
    
    #[@Groups('neo4j')]
    public $name;
    
    #[@Groups('neo4j')]
    #[ORM\...]
    public Parent $parent;
}
```
